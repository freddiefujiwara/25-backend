<?php

class Invitation {
    private $pdo;
    public function __construct(){
        $url = parse_url(getenv('DATABASE_URL'));
        $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));
        $this -> pdo = new PDO($dsn, $url['user'], $url['pass']);
    }
    public function issueAHash($length = 16){
        return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, $length);
    }
    public function validateForUserId($userId){
        return 1 == preg_match('/^[a-zA-Z0-9]{1,16}$/',$userId);
    }
    public function checkExistence($userId){
        $sql = "SELECT count(*) FROM ".getenv('TABLE_NAME')." WHERE user_id = '$userId'";
        $result = $this -> pdo->prepare($sql);
        $result->execute();
        return 1 == $result->fetchColumn(); 
    }
}

