<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

  <title>Thank you</title>
</head>

<body>
<?php
require '../config/db.php';
require '../src/model/Invitation.php';
$obj = new Invitation(); 
try{
    $obj -> create(trim($_POST['user_id']),$_POST['hash']);
    echo 'Thank you!!';

}catch(Exception $e){
    echo $e -> getMessage();
}
?>
</body>
</html>
