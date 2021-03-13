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
    <title>Отзывы о товаре</title>
</head>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST'):
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";

    $user = "root";
    $pass = "admin";
    $host = "localhost";
    $db = "agregator";

    if (isset($_POST["author"]) AND isset($_POST["review"])) {
        if (!empty($_POST["author"]) AND !empty($_POST["review"])) {
            $author = $_POST["author"];
            $review = $_POST["review"];
            $rating = $_POST["rating"] ?? '5';
        } else {
            throw new Exception('you have empty fields in action_update');
        }
    } else {
        throw new Exception('you did not set task fields in action_update');
    }

    if (isset($_POST["product_name"])) {
        $product_name = $_POST["product_name"];
    } else {
        throw new Exception('you not have a product');
    }

    if (isset($_POST['id']) AND !empty($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        throw new Exception('you are not set an ID of product!');
    }

    if (isset($_POST['photo']) AND !empty($_POST['photo'])) {
        $photo = $_POST['photo'];
    } else {
        $photo = "img/orig/nothing.jpg";
    }

    $product_name = htmlspecialchars($product_name, ENT_QUOTES);
    $author = htmlspecialchars($author, ENT_QUOTES);
    $review = htmlspecialchars($review, ENT_QUOTES);
    $rating = htmlspecialchars($rating, ENT_QUOTES);

    $link = mysqli_connect($host, $user, $pass, $db) or die("Error " . mysqli_error($link));

    mysqli_set_charset($link, 'utf8');
    $query = "SELECT * FROM product WHERE title='" . $product_name . "';";
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    if ($result) {
        $row = mysqli_fetch_row($result);
        $product_id = $row[0];
    } else {
        throw new Exception('you not have a product with such title!');
    }
    $query = "INSERT INTO `review` (`product_id`,`author`,`rating`,`comment_text`)  VALUES (?, ?, ?, ?);";

    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'isis', $product_id, $author, $rating, $review);
    mysqli_stmt_execute($stmt) or die("Ошибка " . mysqli_error($link));
    mysqli_stmt_close($stmt);

    mysqli_close($link);

    ?>
    <h1 class="text-center mb-4">Отзыв добавлен!</h1>
    <form method="get" action="reviews.php">
        <input type="hidden" id="prod" name="prod" value="<?php echo $product_name; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $id; ?>">
        <input type="hidden" id="photo" name="photo" value="<?php echo $photo; ?>">
        <button type="submit" class="btn btn-success  w-100">
            OK
        </button>
    </form>
<?php else:

if (isset($_GET['id']) AND !empty($_GET['id'])) {
    $id = $_GET['id'];
} else {
    throw new Exception('you are not set an ID of product!');
}

if (isset($_GET['prod']) AND !empty($_GET['prod'])) {
    $prod = $_GET['prod'];
} else {
    throw new Exception('you have not chosen a product!');
}

if (isset($_GET['photo']) AND !empty($_GET['photo'])) {
    $photo = $_GET['photo'];
} else {
    $photo = "img/orig/nothing.jpg";
}

?>
    <form method="post" action='reviews.php'>
        <input type="hidden" id="product_name" name="product_name" value="<?php echo $prod; ?>">
        <input type="hidden" id="photo" name="photo" value="<?php echo $photo; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $id; ?>">
        <div class="form-row">
            <div class="form-group col-md-12 mb-0">
                <textarea class="form-control" rows="3" id="review" name="review" placeholder="текст отзыва..."
                          required></textarea>
            </div>
        </div>
        <div class="form-row align-items-end">
            <div class="form-group col-md-9">
                <label for="author" class="col-form-label mr-1">Имя:</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <div class="form-group col-md-1">
                <label for="rating" class="col-form-label mr-1">Оценка:</label>
                <select class="form-control" id="rating" name="rating" required>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                    <option>6</option>
                    <option>7</option>
                    <option>8</option>
                    <option>9</option>
                    <option>10</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <button type="submit" class="btn btn-primary">Добавить отзыв</button>
            </div>
        </div>

    </form>

<?php

$user = "root";
$pass = "admin";
$host = "localhost";
$db = "agregator";
$data = [];
$rating_sum = 0;
$avg_rating = 0;

$link = mysqli_connect($host, $user, $pass, $db) or die("Error " . mysqli_error($link));
mysqli_set_charset($link, 'utf8');

$query = "SELECT * FROM review WHERE product_id=$id;";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
if ($result) {
    $rows = mysqli_num_rows($result);

    for ($i = 0; $i < $rows; ++$i) {
        $row = mysqli_fetch_row($result);
        $data[$i]["author"] = $row[2];
        $data[$i]["rating"] = $row[3];
        $data[$i]["coment_text"] = $row[4];
        $data[$i]["create_at"] = $row[5];
        $rating_sum += $row[3];
    }
    if ($rows > 0) {
        $avg_rating = round($rating_sum / $rows, 2);
    }
}
?>
    <div class="d-flex justify-content-center">
        <h1 class="display-1 m-3"><u><?php echo $prod ?></u></h1>
    </div>
    <div class="d-flex justify-content-center">
        <?php echo "<div><img src='$photo' alt='product img' height='150' width='200'></div>"; ?>
        <h1 class="display-1 ml-5"><?php echo $avg_rating ?></h1>
    </div>
<?php
echo <<<EOD
<table class="table_sort table">
<thead class="table-success">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Автор</th>
            <th scope="col">Рейтинг</th>
            <th scope="col">Комментарий</th>
            <th scope="col">Дата добавления</th>
        </tr>
    </thead>
    <tbody class="table-hover">
EOD;

for ($i = 0; $i < count($data); $i++) {
    $nomstr = $i + 1;
    echo <<<EOD
        <tr class="table-light">
            <th scope="row" class="table-success">{$nomstr}</th>
            <td class="table-light">{$data[$i]["author"]}</td>
            <td class="table-light">{$data[$i]["rating"]}</td>
            <td class="table-light">{$data[$i]["coment_text"]}</td>
            <td class="table-light">{$data[$i]["create_at"]}</td>
        </tr>
    EOD;
}
echo '</tbody><br></table><br>';

?>
    <script src="js/sort_table.js"></script>

<?php endif; ?>
</body>
</html>

