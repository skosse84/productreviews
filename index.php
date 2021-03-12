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
<h1 class="text-center">Товары:</h1>
<?php

$user = "root";
$pass = "admin";
$host = "localhost";
$db = "agregator";
$date = [];

$link = mysqli_connect($host, $user, $pass, $db) or die("Error " . mysqli_error($link));
mysqli_set_charset($link, 'utf8');

$query = "SELECT * FROM product;";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
if ($result) {
    $rows = mysqli_num_rows($result); // количество полученных строк

    for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        $data[$i]["title"] = $row[1];
        $data[$i]["price"] = $row[2];
        $data[$i]["img"] = $row[3];
        $data[$i]["description"] = $row[4];
        $data[$i]["author"] = $row[5];
        $data[$i]["rev_count"] = $row[6];
        $data[$i]["date"] = $row[7];
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
    <tbody>
EOD;

for ($i = 0; $i < count($data); $i++) {
    $nomstr = $i + 1;
    echo <<<EOD
        <tr>
            <th scope="row" class="table-success">{$nomstr}</th>
            <td class="table-light">{$data[$i]["title"]}</td>
            <td class="table-light">{$data[$i]["price"]}</td>
            <td class="table-light">{$data[$i]["img"]}</td>
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