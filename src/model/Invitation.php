<?php
class Invitation {
    private $pdo;
    public function __construct(){
        $url = parse_url(getenv('DATABASE_URL'));
        $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));
        $this -> pdo = new PDO($dsn, $url['user'], $url['pass']);
        $this -> pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function issueAHash($length = 16){
        return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, $length);
    }
    public function validateForUserId($userId){
        return 1 == preg_match('/^[a-zA-Z0-9]{1,16}$/',$userId);
    }
    public function checkExistence($userId){
        $sql = "SELECT COUNT(user_id) FROM ".getenv('TABLE_NAME')." WHERE user_id=:user_id";
        $sql .= " AND   hash       IS NULL";
        $sql .= " AND   invited_to IS NULL";
        $sql .= " AND   issued_at  IS NULL";
        $sql .= " AND   clicked_at IS NULL";
        $sql .= " AND   invited_at IS NULL";
        $statement  = $this -> pdo -> prepare($sql);
        $statement -> execute(array('user_id'=>$userId));
        return 0 <  $statement -> fetchColumn();
    }
    public function loginAndIssue($userId){
        if(!$this -> validateForUserId($userId)){
            throw new Exception('Invalid'); 
        }
        if(!$this -> checkExistence($userId)){
            throw new Exception('NotExist'); 
        }
        $hash = $this -> issueAHash();
        $sql = "INSERT INTO ".getenv('TABLE_NAME')." (user_id,hash,issued_at) VALUES (:user_id,:hash,NOW())";
        $statement  = $this -> pdo -> prepare($sql);
        $statement -> execute(array('user_id'=>$userId,'hash' => $hash));
        return $hash;
    }
    public function click($hash){
        $sql = "UPDATE ".getenv('TABLE_NAME')." SET clicked_at=NOW() WHERE hash=:hash";
        $sql .= " AND   user_id    IS NOT NULL";
        $sql .= " AND   invited_to IS NULL";
        $sql .= " AND   issued_at  IS NOT NULL";
        $sql .= " AND   clicked_at IS NULL";
        $sql .= " AND   invited_at IS NULL";
        $statement  = $this -> pdo -> prepare($sql);
        $statement -> execute(array('hash' => $hash));
        if( 1 !=  $statement -> rowCount()){
            throw new Exception('NotExist'); 
        }
    }
    public function create($userId, $hash = null){
        if(!$this -> validateForUserId($userId)){
            throw new Exception('Invalid'); 
        }
        if($this -> checkExistence($userId)){
            throw new Exception('AlreadyExist'); 
        }
        if(!empty($hash)){
            $sql = "UPDATE ".getenv('TABLE_NAME')." SET invited_to=:user_id,invited_at=NOW() WHERE hash=:hash";
            $sql .= " AND   user_id    IS NOT NULL";
            $sql .= " AND   invited_to IS NULL";
            $sql .= " AND   issued_at  IS NOT NULL";
            $sql .= " AND   clicked_at IS NOT NULL";
            $sql .= " AND   invited_at IS NULL";
            $statement  = $this -> pdo -> prepare($sql);
            $statement -> execute(array('user_id'=>$userId,'hash' => $hash));
            if( 1 !=  $statement -> rowCount()){
                throw new Exception('Invalid'); 
            }
        }
        $sql = "INSERT INTO ".getenv('TABLE_NAME')." (user_id) VALUES ('$userId')";
        $statement  = $this -> pdo -> prepare($sql);
        $statement -> execute();
    }

    public function dump(){
        $sql  = "SELECT user_id,invited_to,invited_at FROM ".getenv('TABLE_NAME');
        $sql .= " WHERE user_id    IS NOT NULL";
        $sql .= " AND   hash       IS NOT NULL";
        $sql .= " AND   invited_to IS NOT NULL";
        $sql .= " AND   issued_at  IS NOT NULL";
        $sql .= " AND   clicked_at IS NOT NULL";
        $sql .= " AND   invited_at IS NOT NULL";
        return $this -> pdo -> query($sql) -> fetchAll(PDO::FETCH_ASSOC);
    }
}
