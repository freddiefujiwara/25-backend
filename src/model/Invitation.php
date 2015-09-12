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
        $stmt = $this -> pdo->prepare($sql);
        $stmt->execute();
        return 0 < $stmt->fetchColumn(); 
    }
    public function loginAndIssue($userId){
        if(!$this -> validateForUserId($userId)){
            throw new Exception('NotValid'); 
        }
        if(!$this -> checkExistence($userId)){
            throw new Exception('NotExist'); 
        }
        $hash = $this -> issueAHash();
        $this -> pdo -> query("INSERT INTO ".getenv('TABLE_NAME')." (user_id,hash,created_at) VALUES ('$userId','$hash',NOW())");
        return $hash;
    }
    public function click($hash){
        $sql = "UPDATE ".getenv('TABLE_NAME')." SET clicked_at = NOW() WHERE hash = '$hash'";
        $stmt = $this -> pdo->prepare($sql);
        $stmt->execute();
        if( 1 !=  $stmt->rowCount()){
            throw new Exception('NotExist'); 
        }
    }
}
