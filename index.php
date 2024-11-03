<?php
	session_start();
?>
<!doctype html>
<html lang="ru" data-bs-theme="dark">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Тестовое задание</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
	<div class="container">

		<?php if (isset($_SESSION['warning'])) { ?>
        <div class="alert alert-warning">
            <?php foreach ($_SESSION['warning'] as $warning_message){ ?>
                <?php echo $warning_message; ?><br>
            <?php } ?>
            <?php unset($_SESSION['warning']); ?>
        </div>
    	<?php } ?>

		<?php if (isset($_SESSION['message'])) { ?>
			<div class="alert alert-success">
				<?php echo $_SESSION['message']; ?>
				<?php unset($_SESSION['message']); ?>
			</div>
		<?php } ?>

		<?php if (isset($_SESSION['error'])) { ?>
			<div class="alert alert-danger">
				<?php echo $_SESSION['error']; ?>
				<?php unset($_SESSION['error']); ?>
			</div>
		<?php } ?>

		<form action="store.php" method="POST">

			<div class="mb-3">
				<label for="event_id" class="form-label">event_id</label>
				<input type="text" class="form-control" id="event_id" name="event_id">
			</div>
			<div class="mb-3">
				<label for="event_date" class="form-label">event_date</label>
				<input type="text" class="form-control" id="event_date" name="event_date">
			</div>

			<div class="mb-3">
				<label for="ticket_adult_price" class="form-label">ticket_adult_price</label>
				<input type="text" class="form-control" id="ticket_adult_price" name="ticket_adult_price">
			</div>
			<div class="mb-3">
				<label for="ticket_adult_quantity" class="form-label">ticket_adult_quantity</label>
				<input type="text" class="form-control" id="ticket_adult_quantity" name="ticket_adult_quantity">
			</div>

			<div class="mb-3">
				<label for="ticket_kid_price" class="form-label">ticket_kid_price</label>
				<input type="text" class="form-control" id="ticket_kid_price" name="ticket_kid_price">
			</div>
			<div class="mb-3">
				<label for="ticket_kid_quantity" class="form-label">ticket_kid_quantity</label>
				<input type="text" class="form-control" id="ticket_kid_quantity" name="ticket_kid_quantity">
			</div>

			<button type="submit" class="btn btn-primary">Добавить</button>

		</form>
	</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
