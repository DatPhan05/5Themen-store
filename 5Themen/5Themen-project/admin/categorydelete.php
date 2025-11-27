<?php
require_once "../include/session.php";
require_once "../include/database.php";
require_once __DIR__ . "/class/category_class.php";

$redirect_url = "categorylist.php";

$id = isset($_REQUEST['category_id']) ? (int)$_REQUEST['category_id'] : 0;

if ($id > 0) {
    $cg     = new Category();
    $result = $cg->delete_category($id);

    if ($result) {
        header("Location: {$redirect_url}?msg=deleted");
        exit;
    } else {
        header("Location: {$redirect_url}?msg=has_child");
        exit;
    }
}

header("Location: {$redirect_url}");
exit;
?>
