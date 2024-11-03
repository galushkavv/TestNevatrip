<?php
    // Функция мокирования API
    function mockApiBook($request)
    {
        $data = json_decode($request, true);

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            return json_encode(['error' => 'Неправильный формат запроса']);
        }

        $requiredFields = [ 'event_id',
                            'event_date',
                            'ticket_adult_price',
                            'ticket_adult_quantity',
                            'ticket_kid_price',
                            'ticket_kid_quantity',
                            'barcode'];

        foreach ($requiredFields as $field)
        {
            if (!isset($data[$field]))
            {
                return json_encode(['error' => "Отсутствует поле '$field'"]);
            }
        }
        
        $success = rand(0, 100) < 70; // шанс получить успех 70%

        if ($success)
        {
            return json_encode(['message' => 'order successfully booked']);
        }
        else
        {
            return json_encode(['error' => 'barcode already exists']);
        }
    }

    function mockApiApprove($request)
    {
        $data = json_decode($request, true);

        if (json_last_error() !== JSON_ERROR_NONE)
        {
            return json_encode(['error' => 'Неправильный формат запроса']);
        }

        if (!isset($data['barcode']))
        {
            return json_encode(['error' => "Отсутствует поле 'barcode'"]);
        }

        $responses = [
            ['message' => 'order successfully approved'],
            ['error'   => 'event cancelled'],
            ['error'   => 'no tickets'],
            ['error'   => 'no seats'],
            ['error'   => 'fan removed']
        ];

        $random_index = rand(0, count($responses) - 1);

        return json_encode($responses[$random_index]);
    }