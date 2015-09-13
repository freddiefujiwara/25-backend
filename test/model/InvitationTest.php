<?php
require 'src/model/Invitation.php';
require 'config/db.php';


class InvitationTest extends PHPUnit_Framework_TestCase {
    private $pdo;
    private $obj;
    public function setup(){
        $url = parse_url(getenv('DATABASE_URL'));
        $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1)); 
        $this -> pdo = new PDO($dsn, $url['user'], $url['pass']);
        $this -> pdo -> query('DROP TABLE IF EXISTS invitations_test');
        $this -> pdo -> query(file_get_contents('config/invitations_test.sql'));
        $this -> obj = new Invitation();
    }
    public function tearDown(){
//        $this -> pdo -> query('DROP TABLE IF EXISTS invitations_test');
    }
    public function testConstructorAndConfig(){
        $this->assertNotNull($this -> obj);
        $this->assertTrue(get_class($this -> obj) == "Invitation");

        $this->assertNotNull(getenv('DATABASE_URL'));
        $this->assertTrue(1 == preg_match('/^postgres:/',getenv('DATABASE_URL')));
        $this->assertNotNull(getenv('TABLE_NAME'));
        $this->assertTrue(1 == preg_match('/^invitations/',getenv('TABLE_NAME')));
    }

    public function testRandom(){
        $this->assertTrue(method_exists($this -> obj,"issueAHash"));

        $hash1  = $this -> obj -> issueAHash();
        $this->assertTrue(strlen($hash1) == 16 );
        $hash2  = $this -> obj -> issueAHash();
        $this->assertTrue(strlen($hash2) == 16 );

        $this->assertFalse($hash1 == $hash2);
        $hash3  = $this -> obj -> issueAHash(30);
        $this->assertTrue(strlen($hash3) == 30 );
    }

    public function testValidateForUserID(){
        $this->assertTrue(method_exists($this -> obj,"validateForUserId"));

        $this->assertTrue($this -> obj -> validateForUserId('freddiefujiwara'));
        $this->assertTrue($this -> obj -> validateForUserId('1234567890123456'));

        $this->assertFalse($this -> obj -> validateForUserId('freddiefujiwara '));
        $this->assertFalse($this -> obj -> validateForUserId('freddiefujiwara@'));
        $this->assertFalse($this -> obj -> validateForUserId(''));
        $this->assertFalse($this -> obj -> validateForUserId('12345678901234567'));
    }

    public function testCheckExistence(){
        $this->assertTrue(method_exists($this -> obj,"checkExistence"));

        $this -> pdo -> query("INSERT INTO invitations_test (user_id) VALUES ('freddiefujiwara')");
        $this->assertTrue($this -> obj -> checkExistence('freddiefujiwara'));
        $this->assertFalse($this -> obj -> checkExistence('freddiefujiwara123'));
    }

    public function testLoginAndIssueWithNotExist(){
        $this->assertTrue(method_exists($this -> obj,"loginAndIssue"));

        $this -> pdo -> query("INSERT INTO invitations_test (user_id) VALUES ('freddiefujiwara')");
        $this->setExpectedException('Exception','NotExist');
        $this -> obj -> loginAndIssue('test');
    }
    public function testLoginAndIssueWithInvalid(){
        $this->assertTrue(method_exists($this -> obj,"loginAndIssue"));

        $this->setExpectedException('Exception','Invalid');
        $this -> obj -> loginAndIssue('#');
    }
    public function testLoginAndIssue(){
        $this->assertTrue(method_exists($this -> obj,"loginAndIssue"));

        $this -> pdo -> query("INSERT INTO invitations_test (user_id) VALUES ('freddiefujiwara')");
        $this -> obj -> loginAndIssue('freddiefujiwara');
        $sql = "SELECT COUNT(user_id) FROM ".getenv('TABLE_NAME')." WHERE user_id = 'freddiefujiwara'";
        $this -> assertTrue(2 == $this -> pdo->query($sql)->fetchColumn());
    }

    public function testClick(){
        $this->assertTrue(method_exists($this -> obj,"click"));
        $this -> pdo -> query("INSERT INTO invitations_test (user_id) VALUES ('freddiefujiwara')");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,hash,issued_at) VALUES ('freddiefujiwara','cxn3zm5lkhp4uy7j',NOW())");

        $this -> obj -> click('cxn3zm5lkhp4uy7j');
        $sql = "SELECT clicked_at FROM ".getenv('TABLE_NAME')." WHERE hash = 'cxn3zm5lkhp4uy7j'";
        $stmt = $this -> pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt -> fetch(PDO::FETCH_ASSOC);
        $this -> assertNotNull($result['clicked_at']);
    }
    public function testClickWithNotExist(){
        $this->assertTrue(method_exists($this -> obj,"click"));
        $this -> pdo -> query("INSERT INTO invitations_test (user_id) VALUES ('freddiefujiwara')");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,hash,issued_at) VALUES ('freddiefujiwara','cxn3zm5lkhp4uy7j',NOW())");

        $this->setExpectedException('Exception','NotExist');
        $this -> obj -> click('cxn3zm5lkhp4uy7');
    }

    public function testCreate(){
        $this->assertTrue(method_exists($this -> obj,"create"));
        $this -> obj -> create("freddiefujiwara");

        $sql = "SELECT count(*) FROM ".getenv('TABLE_NAME')." WHERE user_id = 'freddiefujiwara'";
        $this -> assertTrue(1 == $this -> pdo->query($sql)->fetchColumn());

        $this -> pdo -> query("INSERT INTO ".getenv('TABLE_NAME')." (user_id,hash,issued_at,clicked_at) VALUES ('freddiefujiwara','cxn3zm5lkhp4uy7j',NOW(),NOW())");
        $this -> obj -> create("fujiwarafreddie","cxn3zm5lkhp4uy7j");

        $sql = "SELECT count(*) FROM ".getenv('TABLE_NAME')." WHERE user_id = 'fujiwarafreddie'";
        $this -> assertTrue(1 == $this -> pdo->query($sql)->fetchColumn());

        $sql = "SELECT count(*) FROM ".getenv('TABLE_NAME')." WHERE invited_to = 'fujiwarafreddie'";
        $this -> assertTrue(1 == $this -> pdo->query($sql)->fetchColumn());
    }
    public function testDump(){
        $this->assertTrue(method_exists($this -> obj,"dump"));
        $this -> pdo -> query("INSERT INTO invitations_test (user_id) VALUES ('freddiefujiwara')");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,hash,issued_at) VALUES ('freddiefujiwara','cxn3zm5lkhp4uy7j',NOW())");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,hash,issued_at,clicked_at) VALUES ('freddiefujiwara','cxn3zm5lkhp4uy7a',NOW(),NOW())");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,hash,issued_at,clicked_at,invited_at,invited_to) VALUES ('freddiefujiwara','bxn3zm5lkhp4uy7j',NOW(),NOW(),NOW(),'fujiwarafreddie')");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id) VALUES ('fujiwarafreddie')");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id,hash,issued_at,clicked_at,invited_at,invited_to) VALUES ('freddiefujiwara','axn3zm5lkhp4uy7j',NOW(),NOW(),NOW(),'fujiwara')");
        $this -> pdo -> query("INSERT INTO invitations_test (user_id) VALUES ('fujiwara')");

        $dump = $this -> obj -> dump();
        $this -> assertTrue(is_array($dump));
        $this -> assertTrue(2 == count($dump));
        $this -> assertTrue(3 == count($dump[0]));
        $this -> assertTrue(isset($dump[0]['user_id']));
        $this -> assertTrue(isset($dump[0]['invited_to']));
        $this -> assertTrue(isset($dump[0]['invited_at']));
    }
}
