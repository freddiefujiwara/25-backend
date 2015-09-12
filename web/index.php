<?php
require '../config/db.php';
require '../src/model/Invitation.php';
$ins = new Invitation();
$ins -> issueAHash();
echo getenv('DATABASE_URL');
echo getenv('TABLE_NAME');
