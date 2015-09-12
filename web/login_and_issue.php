<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

  <title>Issued</title>
</head>

<body>
<?php
require '../config/db.php';
require '../config/url.php';
require '../src/model/Invitation.php';
$obj = new Invitation(); 
try{
    $hash = $obj -> loginAndIssue(trim($_POST['user_id']));
    $user_id = htmlspecialchars($_POST['user_id'], ENT_QUOTES);
    $url="$redirecterUrl?hash=$hash&url=$contentsUrl".urlencode("?hash=$hash");
    $html = <<<EOM
    <h1>HELLO $user_id Your Invitation Code Is Here<h1>
    <textarea>
$url
    </textarea>
EOM;
    echo $html;

}catch(Exception $e){
    echo $e -> getMessage();
}
?>
</body>
</html>
<?php
