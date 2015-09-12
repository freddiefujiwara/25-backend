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
        $sql = "SELECT COUNT(user_id) FROM ".getenv('TABLE_NAME')." WHERE user_id='$userId'";
        $sql .= " AND   hash       IS NULL";
        $sql .= " AND   invited_to IS NULL";
        $sql .= " AND   issued_at  IS NULL";
        $sql .= " AND   clicked_at IS NULL";
        $sql .= " AND   invited_at IS NULL";
        return 0 <  $this -> pdo -> query($sql) -> fetchColumn();
    }
    public function loginAndIssue($userId){
        if(!$this -> validateForUserId($userId)){
            throw new Exception('NotValid'); 
        }
        if(!$this -> checkExistence($userId)){
            throw new Exception('NotExist'); 
        }
        $hash = $this -> issueAHash();
        $this -> pdo -> query("INSERT INTO ".getenv('TABLE_NAME')." (user_id,hash,issued_at) VALUES ('$userId','$hash',NOW())");
        return $hash;
    }
    public function click($hash){
        $sql = "UPDATE ".getenv('TABLE_NAME')." SET clicked_at=NOW() WHERE hash='$hash'";
        $sql .= " AND   user_id    IS NOT NULL";
        $sql .= " AND   invited_to IS NULL";
        $sql .= " AND   issued_at  IS NOT NULL";
        $sql .= " AND   clicked_at IS NULL";
        $sql .= " AND   invited_at IS NULL";
        if( 1 !=  $this -> pdo 
            -> query($sql)
            -> rowCount()){
            throw new Exception('NotExist'); 
        }
    }
    public function create($userId, $hash = null){
        if(!$this -> validateForUserId($userId)){
            throw new Exception('NotValid'); 
        }
        if($this -> checkExistence($userId)){
            throw new Exception('AlreadyExist'); 
        }
        if(!empty($hash)){
            $sql .= "UPDATE ".getenv('TABLE_NAME')." SET invited_to='$userId' ,invited_at=NOW() WHERE hash='$hash'";
            $sql .= " AND   user_id    IS NOT NULL";
            $sql .= " AND   invited_to IS NULL";
            $sql .= " AND   issued_at  IS NOT NULL";
            $sql .= " AND   clicked_at IS NOT NULL";
            $sql .= " AND   invited_at IS NULL";
            if( 1 !=  $this -> pdo 
                -> query($sql)
                -> rowCount()){
                throw new Exception('NotValid'); 
            }
        }
        $this -> pdo -> query("INSERT INTO ".getenv('TABLE_NAME')." (user_id) VALUES ('$userId')");
    }

    public function dump(){
        $sql  = "SELECT user_id,invited_to,invited_at FROM ".getenv('TABLE_NAME');
        $sql .= " WHERE user_id     IS NOT NULL";
        $sql .= " AND   hash       IS NOT NULL";
        $sql .= " AND   invited_to IS NOT NULL";
        $sql .= " AND   issued_at  IS NOT NULL";
        $sql .= " AND   clicked_at IS NOT NULL";
        $sql .= " AND   invited_at IS NOT NULL";
        return $this -> pdo -> query($sql) -> fetchAll(PDO::FETCH_ASSOC);
    }
}
