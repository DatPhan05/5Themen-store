<?php
require_once __DIR__ . "/class/category_content_class.php";
$ct = new CategoryContent();

$id = $_GET['id'];
$ct->delete($id);

header("Location: category_content_list.php");
exit;
