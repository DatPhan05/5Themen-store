<?php
require_once __DIR__ . "/Class/product_class.php";
$id = (int)($_GET['id'] ?? 0);
if($id){ (new Product())->delete_product($id); }
header("Location: productlist.php"); exit;
