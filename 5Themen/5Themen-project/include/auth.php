<?php
require_once __DIR__ . '/session.php';

function require_user_login(){
    if(!Session::get('user_login')){
        header("Location: login.php");
        exit;
    }
}
?>
