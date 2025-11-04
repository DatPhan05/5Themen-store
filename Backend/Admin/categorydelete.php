<?php
require_once __DIR__ . "/Class/category_class.php";
$cg = new category();
$id = (int)($_GET['category_id'] ?? 0);
if($id){ $cg->delete_category($id); }
header("Location: categorylist.php");
exit;
