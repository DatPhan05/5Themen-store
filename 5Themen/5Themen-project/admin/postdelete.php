<?php
require_once __DIR__ . "/Class/post_class.php";

$postModel = new Post();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $postModel->delete_post($id);
}

header("Location: postlist.php");
exit;
