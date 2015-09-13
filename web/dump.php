<?php
require '../config/db.php';
require '../src/model/Invitation.php';
$obj = new Invitation();
try{
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=dump.csv');
    header('Pragma: no-cache');
    foreach($obj -> dump() as $row){
        echo join($row,',')."\n";
    }
}catch(Exception $e){
    echo $e -> getMessage();
}
