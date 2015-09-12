<?php
require '../src/model/Invitation.php';
$ins = new Invitation();
$ins -> issueAHash();
$url = parse_url(getenv('DATABASE_URL'));
print_r($url);
