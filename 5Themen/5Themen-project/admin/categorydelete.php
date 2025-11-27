<?php
include "../include/session.php";
include "../include/database.php";
require_once __DIR__ . "/class/category_class.php";

$redirect_url = "categorylist.php";

if (!isset($_REQUEST['category_id'])) {
    header("Location: " . $redirect_url);
    exit;
}

$id = (int)($_REQUEST['category_id']);

if ($id > 0) {
    $cg = new Category();
    $result = $cg->delete_category($id);

    if ($result) {
        header("Location: {$redirect_url}?msg=deleted");
        exit;
    } else {
        header("Location: {$redirect_url}?msg=has_child");
        exit;
    }
} else {
    header("Location: " . $redirect_url);
    exit;
}
