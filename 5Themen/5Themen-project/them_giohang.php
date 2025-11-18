<?php
session_start();

$id     = $_POST['id'];
$name   = $_POST['name'];
$image  = $_POST['image'];
$price  = $_POST['price'];
$color  = $_POST['color'];
$size   = $_POST['size'];
$qty    = $_POST['qty'];

$_SESSION['cart'][$id] = [
    "name"  => $name,
    "image" => $image,
    "price" => $price,
    "color" => $color,
    "size"  => $size,
    "qty"   => $qty
];

header("Location: giohang.php");
exit;
