<?php
require_once __DIR__ . "/Class/brand_class.php";
$id = (int)($_GET['brand_id'] ?? 0);
if($id){ (new Brand())->delete_brand($id); }
header("Location: brandlist.php"); exit;
