<?php
require 'src/model/Invitation.php';
require 'config/db.php';


class InvitationTest extends PHPUnit_Framework_TestCase {
    private $pdo;
    public function setup(){
        $url = parse_url(getenv('DATABASE_URL'));
        $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1)); 
        $this -> pdo = new PDO($dsn, $url['user'], $url['pass']);
        $this -> pdo -> query('DROP TABLE IF EXISTS invitations_test');
        $this -> pdo -> query(file_get_contents('config/invitations_test.sql'));
    }
    public function tearDown(){
//        $this -> pdo -> query('DROP TABLE IF EXISTS invitations_test');
    }
    public function testConstructor(){
        $ins = new Invitation();
        $this->assertNotNull($ins);
        $this->assertTrue(get_class($ins) == "Invitation");
        $this->assertNotNull(getenv('DATABASE_URL'));
        $this->assertTrue(1 == preg_match('/^postgres:/',getenv('DATABASE_URL')));
        $this->assertNotNull(getenv('TABLE_NAME'));
        $this->assertTrue(1 == preg_match('/^invitations/',getenv('TABLE_NAME')));
    }
    public function testRandom(){
        $ins = new Invitation();
        $this->assertTrue(method_exists($ins,"issueAHash"));
        $hash1  = $ins -> issueAHash();
        $this->assertTrue(strlen($hash1) == 16 );
        $hash2  = $ins -> issueAHash();
        $this->assertTrue(strlen($hash2) == 16 );
        $this->assertFalse($hash1 == $hash2);
        $hash3  = $ins -> issueAHash(30);
        $this->assertTrue(strlen($hash3) == 30 );
    }
    public function testValidateForUserID(){
        $ins = new Invitation();
        $this->assertTrue(method_exists($ins,"validateForUserId"));
        $this->assertTrue($ins -> validateForUserId('freddiefujiwara'));
        $this->assertTrue($ins -> validateForUserId('1234567890123456'));

        $this->assertFalse($ins -> validateForUserId('freddiefujiwara '));
        $this->assertFalse($ins -> validateForUserId('freddiefujiwara@'));
        $this->assertFalse($ins -> validateForUserId(''));
        $this->assertFalse($ins -> validateForUserId('12345678901234567'));
    }

    public function testCheckExistence(){
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,created_at) VALUES ('freddiefujiwara',NOW())");
        $ins = new Invitation();
        $this->assertTrue(method_exists($ins,"checkExistence"));
        $this->assertTrue($ins -> checkExistence('freddiefujiwara'));
        $this->assertFalse($ins -> checkExistence('freddiefujiwara123'));
    }

    public function testLoginAndIssueWithNotExist(){
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,created_at) VALUES ('freddiefujiwara',NOW())");
        $ins = new Invitation();
        $this->assertTrue(method_exists($ins,"loginAndIssue"));
        $this->setExpectedException('Exception','NotExist');
        $ins -> loginAndIssue('test');
    }
    public function testLoginAndIssueWithNotValid(){
        $ins = new Invitation();
        $this->assertTrue(method_exists($ins,"loginAndIssue"));
        $this->setExpectedException('Exception','NotValid');
        $ins -> loginAndIssue('#');
    }
    public function testLoginAndIssue(){
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,created_at) VALUES ('freddiefujiwara',NOW())");
        $ins = new Invitation();
        $this->assertTrue(method_exists($ins,"loginAndIssue"));
        $ins -> loginAndIssue('freddiefujiwara');
        $sql = "SELECT count(*) FROM ".getenv('TABLE_NAME')." WHERE user_id = 'freddiefujiwara'";
        $stmt = $this -> pdo->prepare($sql);
        $stmt->execute();
        $this -> assertTrue(2 == $stmt->fetchColumn());
    }
    public function testClick(){
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,created_at) VALUES ('freddiefujiwara',NOW())");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,hash,created_at) VALUES ('freddiefujiwara','cxn3zm5lkhp4uy7j',NOW())");
        $ins = new Invitation();
        $this->assertTrue(method_exists($ins,"click"));
        $ins -> click('cxn3zm5lkhp4uy7j');
        $sql = "SELECT clicked_at FROM ".getenv('TABLE_NAME')." WHERE hash = 'cxn3zm5lkhp4uy7j'";
        $stmt = $this -> pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        $this -> assertNotNull($result['clicked_at']);
        $ins -> click('cxn3zm5lkhp4uy7j');
    }
    public function testClickWithNotExist(){
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,created_at) VALUES ('freddiefujiwara',NOW())");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,hash,created_at) VALUES ('freddiefujiwara','cxn3zm5lkhp4uy7j',NOW())");
        $ins = new Invitation();
        $this->assertTrue(method_exists($ins,"click"));
        $this->setExpectedException('Exception','NotExist');
        $ins -> click('cxn3zm5lkhp4uy7');
    }
}
