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
    <title>Отзывы</title>
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

    $product_name = htmlspecialchars($product_name, ENT_QUOTES);
    $author = htmlspecialchars($author, ENT_QUOTES);
    $review = htmlspecialchars($review, ENT_QUOTES);
    $rating = htmlspecialchars($rating, ENT_QUOTES);

    $link = mysqli_connect($host, $user, $pass, $db) or die("Error " . mysqli_error($link));

    mysqli_set_charset($link, 'utf8');
    $query = "SELECT * FROM product WHERE title='" . $product_name . "';";
    var_dump($query);
    $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
    if ($result) {
        $row = mysqli_fetch_row($result);
        $product_id = $row[0];
    } else {
        throw new Exception('you not have a product with such title!');
    }
    $query = "INSERT INTO `review` (`product_id`,`author`,`rating`,`comment_text`)  VALUES (?, ?, ?, ?);";

    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'isis', $product_id, $author, $review, $rating);
    mysqli_stmt_execute($stmt) or die("Ошибка " . mysqli_error($link));
    mysqli_stmt_close($stmt);

    mysqli_close($link);

    ?>
    <h1 class="text-center mb-4">Отзыв добавлен!</h1>
    <form method="get" action="reviews.php">
        <input type="hidden" id="prod" name="prod" value="<?php echo $product_name;?>">
        <button type="submit" class="btn btn-success  w-100">
            OK
        </button>
    </form>
<?php else:
    if (isset($_GET['prod']) AND !empty($_GET['prod'])) {
        $prod = $_GET['prod'];
    } else {
        throw new Exception('you have not chosen a product!');
    }

    ?>
    <form method="post" action='reviews.php'>
        <input type="hidden" id="product_name" name="product_name" value="<?php echo $prod; ?>">
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
    <div class="container text-center">
        <h1 class="display-5 m-3"><?php echo $prod ?></h1>
    </div>
<?php endif; ?>


