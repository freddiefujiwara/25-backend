<?php
require '../src/model/Invitation.php'
$ins = new Invitation();
echo $ins -> issueAHash();
