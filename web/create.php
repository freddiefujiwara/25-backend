<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

  <title>Create Your Account</title>
</head>

<body>
  <form action="thankyou.php" method="post">
    <input type="hidden" name="hash" value=
    "<?php echo htmlspecialchars($_GET['hash'], ENT_QUOTES);?>" />
    <input name="user_id" type="text" /> <input type="submit" value=
    "Create Account" />
  </form>
</body>
</html>
