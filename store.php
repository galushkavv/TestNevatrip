<?php
	session_start();

	$barcodeLength = 120; // Длина штрих-кода
	$maxAttempts = 5;     // Максимальное количество попыток бронирования

	require_once('db_settings.php');
	require_once('mock_api.php');

	// Подключение к БД
	$connection = new mysqli($host, $user, $pass, $dbname);

	if ($connection->connect_error)
	{
		$_SESSION['error'] = "Ошибка подключения: " . $connection->connect_error;
    	header("Location: index.php");
   		exit;
	}

	// Приём и фильтрация данных
	$event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
	$event_date = filter_input(INPUT_POST, 'event_date', FILTER_SANITIZE_SPECIAL_CHARS);
	$ticket_adult_price = filter_input(INPUT_POST, 'ticket_adult_price', FILTER_VALIDATE_INT);
	$ticket_adult_quantity = filter_input(INPUT_POST, 'ticket_adult_quantity', FILTER_VALIDATE_INT);
	$ticket_kid_price = filter_input(INPUT_POST, 'ticket_kid_price', FILTER_VALIDATE_INT);
	$ticket_kid_quantity = filter_input(INPUT_POST, 'ticket_kid_quantity', FILTER_VALIDATE_INT);

	if (
		$event_id === false || $event_id < 0 ||
		$ticket_adult_price === false || $ticket_adult_price < 0 ||
		$ticket_adult_quantity === false || $ticket_adult_quantity < 0 ||
		$ticket_kid_price === false || $ticket_kid_price < 0 ||
		$ticket_kid_quantity === false || $ticket_kid_quantity < 0
	) {
		$_SESSION['error'] = "Ошибка: Введены некорректные данные.";
		header("Location: index.php");
		exit;
	}

	// Проверка даты
	if (!preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $event_date))
	{
		$_SESSION['error'] = "Ошибка: Неверный формат даты. Дата должна быть в формате ГГГГ-ММ-ДД ЧЧ:ММ:СС.";
		header("Location: index.php");
		exit;
	}
	
	try
	{
		$dateTime = new DateTime($event_date);
		$event_date = $dateTime->format('Y-m-d H:i:s');
	}
	catch (Exception $e)
	{
		$_SESSION['error'] = "Ошибка: Некорректная дата.";
		header("Location: index.php");
		exit;
	}

	// Подготовка запроса для отправки на API

	$barcode = generateBarcode($barcodeLength);

	$data = [
		'event_id' => $event_id,
		'event_date' => $event_date,
		'ticket_adult_price' => $ticket_adult_price,
		'ticket_adult_quantity' => $ticket_adult_quantity,
		'ticket_kid_price' => $ticket_kid_price,
		'ticket_kid_quantity' => $ticket_kid_quantity,
		'barcode' => $barcode
	];

	$bookJsonData = json_encode($data);
	// Имитация первого запроса к API
	$responseJson = mockApiBook($bookJsonData);
	$responseBook = json_decode($responseJson, true);

	$bookLog = []; // Массив для хранений результатов запросов к API, чтобы было видно, что были неудачные попытки

	$attempts = 1; // Одна попытка только что была

	while(isset($responseBook['error']))
	{
		$bookLog[] = "Попытка бронирования со штрих-кодом [" . substr($data['barcode'], 0, 8) . "...] не удалась: " . $responseBook['error'];

		$data['barcode'] = generateBarcode($barcodeLength);
		$bookJsonData = json_encode($data);
		$responseJson = mockApiBook($bookJsonData);
		$responseBook = json_decode($responseJson, true);
		
		if($attempts >= $maxAttempts)
		{
			$_SESSION['error'] = "Не удалось забронировать заказ после $attempts попыток.";
            if (!empty($bookLog))
			{
                $_SESSION['warning'] = $bookLog;
            }
            header("Location: index.php");
            exit;
		}
		$attempts++;
	}

	if (isset($responseBook['message']))
	{
		$bookLog[] = "Попытка бронирования со штрих-кодом [" . substr($data['barcode'], 0, 8) . "...] была успешной ";
		// Бронирование успешно
		// Переходим к подтверждению

		$approveData = ['barcode' => $data['barcode']];
		$approveJsonData = json_encode($approveData);

		// Имитация второго запроса к API
		$approveResponseJson = mockApiApprove($approveJsonData);
		$responseApprove = json_decode($approveResponseJson, true);

		if (isset($responseApprove['error']))
		{
			$_SESSION['error'] = "Ошибка подтверждения: " . $responseApprove['error'];
		}
		elseif (isset($responseApprove['message']))
		{
			// Подтверждение тоже успешно
			// Переходим к сохранению

			$equal_price = $ticket_adult_price * $ticket_adult_quantity + $ticket_kid_price * $ticket_kid_quantity;

			$connection->begin_transaction();

			try
			{
				// Подготовка запроса
				$stmt = $connection->prepare("INSERT INTO orders (event_id, event_date, ticket_adult_price, ticket_adult_quantity, ticket_kid_price, ticket_kid_quantity, equal_price, barcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
				
				if (!$stmt)
					throw new Exception("Ошибка подготовки запроса: " . $connection->error);
				
				// Добавление данных к подготовленному запросу
				$stmt->bind_param("isiiiiis", $event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $equal_price, $data['barcode']);
				
				// Выполнение запроса
				if (!$stmt->execute())
				{
					if ($stmt->errno == 1062) // Проверка ошибк дублирования ключа
					{
						throw new Exception("Ошибка: Дублирование номера заказа.");
					}
					else
					{
						throw new Exception("Ошибка выполнения запроса: " . $stmt->error);
					}
				}
				
				// Фиксация транзакции при успешном выполнении запроса
				$connection->commit();
				
				$_SESSION['message'] = "Запись со штрихкодом [" . substr($data['barcode'], 0, 8) . "...] успешно добавлена в таблицу orders.";
				
			}
			catch (Exception $e)
			{
				// Откат транзакции в случае ошибки
				$connection->rollback();
				
				$_SESSION['error'] = $e->getMessage();
				header("Location: index.php");
				exit;
			}
			finally
			{
				// Закрытие подготовленного запроса
				$stmt->close();
			}
		}
		else
		{
			$_SESSION['error'] = "Неизвестный ответ от API подтверждения.";
		}
	}
	else
	{
		$_SESSION['error'] = "Неизвестный ответ от API бронирования.";
	}

	$connection->close();

	if (!empty($bookLog))
	{
		$_SESSION['warning'] = $bookLog;
	}

	header("Location: index.php");
	exit;

	/**
	 * Генерирует случайный штрих-код заданной длины.
	 *
	 * @param int $length Длина штрих-кода.
	 * @return string Сгенерированный штрих-код.
	 */

	function generateBarcode($length)
	{
		$barcode = '';
		for ($i = 0; $i < $length; $i++)
		{
			$barcode .= rand(0, 9);
		}
		return $barcode;
	}
?>