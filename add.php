<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <title>Добавление товара</title>
</head>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'):

    $user = "root";
    $pass = "admin";
    $host = "localhost";
    $db = "agregator";

    if (isset($_POST["product_name"]) AND  isset($_POST["author_name"])) {
        if (!empty($_POST["product_name"]) AND !empty($_POST["author_name"])) {
            $product_name = $_POST["product_name"];
            $product_img = $_POST["product_img"] ?? '';
            $product_img_ref = $_POST["product_img_ref"] ?? '';
            $product_price = $_POST["product_price"];
            $author_name = $_POST["author_name"];
            $product_descr = $_POST["product_descr"] ?? '';
        } else {
            throw new Exception('you have empty fields in action_update');
        }
    } else {
        throw new Exception('you did not set task fields in action_update');
    }

    $product_name = htmlspecialchars($product_name, ENT_QUOTES);;
    $product_img = htmlspecialchars($product_img, ENT_QUOTES);
    $product_img_ref = htmlspecialchars($product_img_ref, ENT_QUOTES);
    $product_price = htmlspecialchars($product_price, ENT_QUOTES);
    $author_name = htmlspecialchars($author_name, ENT_QUOTES);
    $product_descr = htmlspecialchars($product_descr, ENT_QUOTES);

    $img_name = $product_img ?? $product_img_ref;
    $img_name = 'nothing.jpg';

    $link = mysqli_connect($host, $user, $pass, $db) or die("Error " . mysqli_error($link));

    mysqli_set_charset($link, 'utf8');
    $query = "INSERT INTO `product` (`title`,`img_name`,`price`,`description`,`author`)  VALUES (?, ?, ?, ?, ?);";

    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'ssiss', $product_name, $img_name, $product_price, $author_name, $product_descr);
    mysqli_stmt_execute($stmt) or die("Ошибка " . mysqli_error($link));
    mysqli_stmt_close($stmt);

    mysqli_close($link);

    ?>
    <h1 class="text-center mb-4">Товар добавлен!</h1>
    <form method="get" action="add.php">
        <button type="submit" class="btn btn-success  w-100">
            OK
        </button>
    </form>
<?php else: ?>
<h1 class="display-5 mb-3">Добавить товар:</h1>
<form method="post" action='add.php'>
    <div class="form-group">
        <label for="product_name">Название товара:</label>
        <input type="text" class="form-control" id="product_name" name="product_name" required>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="product_img">Изображение товара:</label>
            <input type="file" class="form-control" id="product_img" name="product_img"
                   accept="image/jpeg,image/png,image/gif">
        </div>
        <div class="form-group col-md-6">
            <label for="product_img_ref">Ссылка на изображение:</label>
            <input type="text" class="form-control" id="product_img_ref" name="product_img_ref">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="product_price">Средняя цена товара:</label>
            <input type="number" class="form-control" id="product_price" name="product_price" required>
        </div>
        <div class="form-group col-md-4">
            <label for="author_name">Имя добавившего товар:</label>
            <input type="text" class="form-control" id="author_name" name="author_name" required>
        </div>
        <div class="form-group col-md-4">
            <label for="product_date">Дата добавления товара:</label>
            <input type="date" class="form-control" id="product_date" name="product_date"
                   value="<?php echo date('Y-m-d'); ?>" required readonly>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="product_descr">Описание товара:</label>
            <textarea class="form-control" rows="3" id="product_descr" name="product_descr" required></textarea>
        </div>
    </div>
    <button type="submit" class="btn btn-primary w-100 my-3">Добавить товар</button>
</form>
<?php endif; ?>