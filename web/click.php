<?php
require '../config/db.php';
require '../src/model/Invitation.php';
$obj = new Invitation();
try{
    $obj -> click($_GET['hash']);
    http_redirect($_GET['url'], array("hash" => $_GET['hash']), true, HTTP_REDIRECT_PERM);
}catch(Exception $e){
    echo $e -> getMessage();
}
