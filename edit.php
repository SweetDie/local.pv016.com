<?php
include($_SERVER['DOCUMENT_ROOT'] . '/options/connection_database.php');
if($_SERVER["REQUEST_METHOD"]=="POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];


    $sql = 'DELETE FROM tbl_products WHERE `tbl_products`.`id` = '.$id;
    $dbh->exec($sql);
    $sql = "INSERT INTO `tbl_products` (`name`, `price`, `datecrate`, `description`) VALUES (:name, :price, NOW(), :description);";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
    $sql = "SELECT LAST_INSERT_ID() as id;";
    $item = $dbh->query($sql)->fetch();
    $insert_id = $item['id'];
    $images = $_POST['images'];
    $count=1;
    foreach ($images as $base64) {
        $dir_save = 'images/';
        $image_name = guidv4(openssl_random_pseudo_bytes(16)) . '.jpeg';
        $uploadfile = $dir_save . $image_name;
        list(, $data) = explode(',', $base64);
        $data = base64_decode($data);
        file_put_contents($uploadfile, $data);
        $sql = 'INSERT INTO tbl_products_images (name, datecreate, priority, product_id) VALUES(:name, NOW(), :priority, :product_id);';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $image_name);
        $stmt->bindParam(':priority', $count);
        $stmt->bindParam(':product_id', $insert_id);
        $stmt->execute();
        $count++;
        header("Location: /");
        exit();
    }

}else{
    $id = $_GET['id'];
    $name = '';
    $price = '';
    $description = '';
    $sql = 'SELECT p.id, p.name, p.price, p.description 
        from tbl_products p
        where p.id=:id;';

    $sth = $dbh->prepare($sql);
    $sth->execute([':id' => $id]);
    if ($row = $sth->fetch()) {
        $name = $row['name'];
        $price = $row['price'];
        $description = $row['description'];
    }
    $sql = "SELECT pi.name, pi.priority 
        FROM tbl_products_images pi
        WHERE pi.product_id=:id
        ORDER BY pi.priority;";
    $sth = $dbh->prepare($sql);
    $sth->execute([':id' => $id]);
    $images = $sth->fetchAll();

}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Головна сторінка</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">


</head>
<body>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/_header.php'); ?>
<div class="container">
    <h1 class="text-center">Змініти продукт</h1>
    <form method="post" enctype="multipart/form-data" class="col-md-6 offset-md-3">
        <div class="mb-3">
            <label for="name" class="form-label">Назва</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo  $name ?>">
            <input type="hidden"  class="form-control" id="id" name="id" value="<?php echo  $id ?>">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Ціна</label>
            <input type="text" class="form-control" id="price" value="<?php echo  $price ?>" name="price">
        </div>
        <div class="row" id="list_images">
            <?php foreach ($images as $image){

                $path = 'images/'.$image['name'];
                $data = file_get_contents($path);
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $base64 ='data:image/' . $type . ';base64,' . base64_encode($data);
                $id = guidv4(openssl_random_pseudo_bytes(16));

                echo ' <div class = "col-md-3 item-image">
    <div class="row">
        <div class="col-6">
            <div class="fs-4 ms-2">
                <label for="'.$id.'">
                    <i class="fa fa-pencil" style="cursor: pointer;" aria-hidden="true"></i>
                </label>
                <input type="file" class="form-control d-none edit" id="'.$id.'">
            </div>
        </div>
        <div class="col-6">
            <div class="text-end fs-4 text-danger me-2 remove">
                <i class="fa fa-times" style="cursor: pointer" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    <div>
        <img src="'.$base64.'" id="'.$id.'_image" alt="photo" width="100%">
        <input type="hidden" id="'.$id.'_file" value="'.$base64.'" name="images[]">
    </div>
</div>';
            } ?>
            <div class="col-md-3" id="selectImages">
                <label for="image" style="cursor: pointer;" class="form-label text-success">
                    <i class="fa fa-plus-square-o" style="font-size:120px" aria-hidden="true"></i>
                </label>
                <input type="file" class="form-control d-none" id="image" multiple>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Опис</label>
            <input type="text" class="form-control" id="description"  value="<?php echo  $description ?>"name="description">
        </div>

        <button type="submit" class="btn btn-primary"> Додати</button>
    </form>
</div>
</body>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery.min.js"></script>

<script>
    function uuidv4() {
        return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }
    $(function (){
        const image = document.getElementById("image");
        image.onchange = function (e) {
            const files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.addEventListener('load', function () {
                    const base64 = reader.result;
                    const id = uuidv4();
                    const data = `
                        <div class="row">
                            <div class="col-6">
                                <div class="fs-4 ms-2">
                                    <label for="${id}">
                                        <i class="fa fa-pencil" style="cursor: pointer;" aria-hidden="true"></i>
                                    </label>
                                    <input type="file" class="form-control d-none edit" id="${id}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end fs-4 text-danger me-2 remove">
                                    <i class="fa fa-times" style="cursor: pointer" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <img src="${base64}" id="${id}_image" alt="photo" width="100%">
                            <input type="hidden" id="${id}_file" value="${base64}" name="images[]">
                        </div>
                    `;
                    const item = document.createElement('div');
                    item.className = "col-md-3 item-image";
                    item.innerHTML = data;
                    $("#selectImages").before(item);
                });
                const file = files[i];
                if (file)
                    reader.readAsDataURL(file);
            }
            image.value = "";
        }
        $("#list_images").on('click', '.remove', function () {
            $(this).closest('.item-image').remove();
        });

        let edit_id = 0;
        const reader = new FileReader();
        reader.addEventListener('load', () => {
            const base64 = reader.result;

            document.getElementById(`${edit_id}_image`).src = base64;
            document.getElementById(`${edit_id}_file`).value = base64;
        });


        $("#list_images").on('change', '.edit', function (e) {
            edit_id = e.target.id;
            const file = e.target.files[0];
            reader.readAsDataURL(file);
            this.value = "";
        });

    });
</script>
</html>