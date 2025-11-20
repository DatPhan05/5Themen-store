<?php
require_once __DIR__ . '/session.php';

function require_user_login(){
    if(!Session::get('is_logged_in')){
        header("Location: login.php");
        exit;
    }
}

?>
