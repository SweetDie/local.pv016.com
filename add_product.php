<?php
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    include './lib/guidv4.php';
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $dir_save = 'images/';
    $image_name = guidv4() . '.jpeg';
    $uploadfile = $dir_save . $image_name;
    echo $uploadfile;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
        include 'connection_database.php';
        $sql = 'INSERT INTO tbl_products (name, image, price, date_create, description) VALUES(:name, :image, :price, NOW(), :description);';
        echo $sql;
        //exit;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':image', $image_name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        echo "Error upload file";
        exit();
    }


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
    <style>
        .drop_container {
            height: 20vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 20px;
        }

        .drop-targets {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            align-items: center;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<?php include "_header.php"; ?>
<div class="container">
    <h1 class="text-center">Додати продукт</h1>
    <form enctype="multipart/form-data" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Назва</label>
            <input type="text" class="form-control" id="name" name="name">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Ціна</label>
            <input type="text" class="form-control" id="price" name="price">
        </div>
        <div class="mb-3">

            <div class="container">
                <div class="row" id="list_images">


                    <div class="col-md-2">
                        <label for="image" style="cursor: pointer" class="form-label ms-2 mt-3 text-success">
                            <i class="fa fa-plus-square-o" style="font-size: 120px;" aria-hidden="true"></i>
                        </label>
                        <input type="file" class="form-control d-none" id="image" name="image" multiple>
                    </div>
                </div>

            </div>


        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Опис</label>
            <input type="text" class="form-control" id="description" name="description">
        </div>

        <button type="submit" class="btn btn-primary">Додати</button>
    </form>

</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/jquery-3.6.2.min.js"></script>


<script>
    function uuidv4() {
        return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }
    $(function(){
        let image = document.getElementById("image");
        const list_iamges = document.getElementById("list_images");
        $("#list_images").on('click', '.remove', function (){
            $(this).closest('.item-image').remove();
        });

        const reader = new FileReader();
        let edit_id=0; //Зберігаю id елемента, якого редагую
        reader.addEventListener("load", () => {
            // convert image file to base64 string
            const base64 = reader.result;
            document.getElementById(`${edit_id}_image`).src=base64;
            document.getElementById(`${edit_id}_file`).src=base64;
        });

        $("#list_images").on('change', '.edit-item', function (e){
            edit_id=e.target.id;
            const file=e.target.files[0];
            reader.readAsDataURL(file);
            this.value="";
        });
        image.onchange = function (e) {
            console.log("Select file");
            const files = e.target.files;
            console.log(files);
            for(let i=0; i<files.length; i++)
            {
                const reader = new FileReader();
                reader.addEventListener("load", () => {
                    // convert image file to base64 string
                    const base64=reader.result;
                    //conosole.log(reader.result);
                    const id=uuidv4();
                    const data = `
                        <div class="row">
                            <div class="col-6">
                                <div class="fs-4 ms-2">
                                    <label for="${id}">
                                        <i class="fa fa-pencil" style="cursor: pointer" aria-hidden="true"></i>
                                    </label>
                                    <input type="file" class="form-control d-none edit-item" id="${id}">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-end fs-4 text-danger me-2 remove">
                                    <i class="fa fa-times" style="cursor: pointer" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <img src="${base64}" id="${id}_image" width="100%"/>
                            <input type="hidden" id="${id}_file" value="${base64}" />
                        </div>
                    `;

                    const item = document.createElement('div');
                    item.className = "col-md-2 item-image";
                    item.innerHTML=data;
                    list_iamges.prepend(item);

                }, false);

                const file = files[i];
                if (file) {
                    const url = URL.createObjectURL(file);
                    reader.readAsDataURL(file);
                }
            }

            image.value = "";
        }
    });

</script>
</body>
</html>
