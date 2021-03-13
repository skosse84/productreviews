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
    <title>Главная</title>
</head>
<body>
<div class="text-right">
<a href="add.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Добавить товар</a>
</div>
<h1 class="text-center">Товары:</h1>
<?php

$user = "root";
$pass = "admin";
$host = "localhost";
$db = "agregator";
$data = [];

$link = mysqli_connect($host, $user, $pass, $db) or die("Error " . mysqli_error($link));
mysqli_set_charset($link, 'utf8');

$query = "select p.*, COUNT(r.id) FROM product p  left join review r ON p.id = r.product_id group by p.title; ";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
if ($result) {
    $rows = mysqli_num_rows($result);

    for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        $data[$i]["id"] = $row[0];
        $data[$i]["title"] = $row[1];
        $data[$i]["price"] = $row[2];
        $data[$i]["img"] = $row[3];
        $data[$i]["description"] = $row[4];
        $data[$i]["author"] = $row[5];
        $data[$i]["date"] = $row[6];
        $data[$i]["rev_count"] = $row[7];
    }
}

echo <<<EOD
<table class="table_sort table">
<thead class="table-success">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">Цена</th>
            <th scope="col">Изображение</th>
            <th scope="col">Описание</th>
            <th scope="col">Дата добавления</th>
            <th scope="col">Имя добавившего</th>
            <th scope="col">Количество отзывов</th>
        </tr>
    </thead>
    <tbody class="table-hover">
EOD;

for ($i = 0; $i < count($data); $i++) {
    $nomstr = $i + 1;
    $file_full_path = __DIR__ . '\\img\\small\\' . $data[$i]["img"];
    $img_path = file_exists($file_full_path) ? 'img/small/' . $data[$i]["img"] : 'img/small/nothing.jpg';
    $photo = file_exists($file_full_path) ? 'img/orig/' . $data[$i]["img"] : 'img/orig/nothing.jpg';
    echo <<<EOD
        <tr class="table-light">
            <th scope="row" class="table-success">{$nomstr}</th>
            <td class="table-light"><a href="reviews.php?prod={$data[$i]["title"]}&photo={$photo}&id={$data[$i]["id"]}">{$data[$i]["title"]}</a></td>
            <td class="table-light">{$data[$i]["price"]}</td>
            <td class="table-light"><img src="{$img_path}" alt="no photo" height="40" width="50"></img></td>
            <td class="table-light">{$data[$i]["description"]}</td>
            <td class="table-light">{$data[$i]["date"]}</td>
            <td class="table-light">{$data[$i]["author"]}</td>
            <td class="table-light">{$data[$i]["rev_count"]}</td>
        </tr>
    EOD;
}
echo '</tbody><br></table><br>';

?>

<script src="js/sort_table.js"></script>
</body>
</html>