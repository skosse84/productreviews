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

    echo "<pre>";
    var_dump($_POST);
    var_dump($_FILES);
    echo "</pre>";

    if (isset($_POST["product_name"]) AND isset($_POST["author_name"])) {
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

    function create_product_image($filename, $file_chaged_name, $width, $height)
    {
        [$widthOrig, $heightOrig] = getimagesize($filename);
        $ext = end(explode('.', $filename));
        $image_p = imagecreatetruecolor($width, $height);
        $image = null;
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
            case 'jfif':
            case 'jpe':
                $image = imagecreatefromjpeg($filename);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $widthOrig, $heightOrig);
                imagejpeg($image_p, $file_chaged_name);
                break;
            case 'png':
                $image = imagecreatefrompng($filename);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $widthOrig, $heightOrig);
                imagepng($image_p, $file_chaged_name);
                break;
            case 'gif':
                $image = imagecreatefromgif($filename);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $widthOrig, $heightOrig);
                imagegif($image_p, $file_chaged_name);
                break;
        }
        if ($image) {
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $widthOrig, $heightOrig);
        } else {
            return false;
        }
        return true;
    }

    function get_image_name($product_name, $file_name)
    {
        $ext = end(explode('.', $file_name));
        return $product_name . "." . $ext;
    }

    if (isset($_FILES["product_img"]) AND !empty($_FILES["product_img"]["name"])) {
        $img_name = get_image_name($product_name, $_FILES['product_img']['name']);
        $temp_file_name = __DIR__ . '\\temp\\' . $img_name;
        $file_orig_name = __DIR__ . '\\img\\orig\\' . $img_name;
        $file_small_name = __DIR__ . '\\img\\small\\' . $img_name;

        if (move_uploaded_file($_FILES['product_img']['tmp_name'], $temp_file_name)) {
            create_product_image($temp_file_name, $file_orig_name, 200, 150);
            create_product_image($temp_file_name, $file_small_name, 50, 40);
        } else {
            $img_name = 'nothing.jpg';
        }
    } elseif (isset($_POST["product_img_ref"])) {
        $url = $_POST["product_img_ref"];
        $img_name = get_image_name($product_name, $url);
        $temp_file_name = __DIR__ . '\\temp\\' . $img_name;
        $file_orig_name = __DIR__ . '\\img\\orig\\' . $img_name;
        $file_small_name = __DIR__ . '\\img\\small\\' . $img_name;
        if (file_put_contents($temp_file_name, file_get_contents($url))) {
            create_product_image($temp_file_name, $file_orig_name, 200, 150);
            create_product_image($temp_file_name, $file_small_name, 50, 40);
        } else {
            $img_name = 'nothing.jpg';
        }
    }

    $img_name = $img_name ?? 'nothing.jpg';

    $link = mysqli_connect($host, $user, $pass, $db) or die("Error " . mysqli_error($link));

    mysqli_set_charset($link, 'utf8');
    $query = "INSERT INTO `product` (`title`,`img_name`,`price`,`description`,`author`)  VALUES (?, ?, ?, ?, ?);";

    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'ssiss', $product_name, $img_name, $product_price, $product_descr, $author_name);
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
<form enctype="multipart/form-data" method="post" action='add.php'>
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
            <input type="date" class="form-control" id="product_date" name="product_date" tabindex="-1"
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