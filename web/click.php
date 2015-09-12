<?php
require '../config/db.php';
require '../src/model/Invitation.php';
$obj = new Invitation();
try{
    $obj -> click($_GET['hash']);
    header('Location: '.$_GET['url'].'?hash='.$_GET['hash']);
}catch(Exception $e){
    echo $e -> getMessage();
}
