<?php
$id = $_GET['id'];
$user = "root";
$pass = "";
$dbh = new PDO('mysql:host=localhost;dbname=pv016', $user, $pass);
echo "<script>console.log('di' );</script>";
if($dbh->query('DELETE FROM tbl_products WHERE id = '.$id)) {
    header('Location: /');
}
$dbh = null;
?>
