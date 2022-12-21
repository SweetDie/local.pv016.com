<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Головна сторінка</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "_header.php"; ?>

<?php
    include_once "connection_database.php";
?>


<div class="container">
    <h1 class="text-center">Список продуктів</h1>
    <section style="background-color: #eee;">
        <div class="container py-5">
            <div class="row">
                <?php
                $sql = "SELECT p.id, p.name, p.price, pi.name as image from tbl_products p, tbl_products_images pi where p.id=pi.product_id and pi.priority=1;";
                foreach($dbh->query($sql) as $row) {
                    $id = $row['id'];
                    $image = $row['image'];
                    $name = $row["name"];
                    $price = $row["price"];
                    //echo "<h1>$id</h1>";
                    echo '
                <div class="col-md-6 col-lg-4 mb-4 mb-md-0">
                    <div class="card">
                        <img src="images/' . $image . '"
                             class="card-img-top" alt="Клавіатура"/>
                        <div class="card-body">

                            <div class="d-flex justify-content-between mb-3">
                                <h5 class="mb-0">' . $name . '</h5>
                            </div>

                            <div class="mb-2 d-flex justify-content-between">
                                <h5 class="text-dark mb-0">' . $price . '₴</h5>
                                <a href="delete.php?id='.$id.'" class="btn btn-danger">Видалити</a>
                                <a href="product.php?id='.$id.'" class="btn btn-success">Купить</a>
                            </div>
                        </div>
                    </div>
                </div>
                    ';
                }
                ?>git init
            </div>
        </div>
    </section>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
