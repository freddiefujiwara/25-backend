<?php
require 'src/model/Invitation.php';
require 'config/db.php'


class InvitationTest extends PHPUnit_Framework_TestCase {
    public function testConstructor(){
        $ins = new Invitation();
        $this->assertNotNull($ins);
        $this->assertTrue(get_class($ins) == "Invitation");
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
        $this->assertFalse($ins -> validateForUserId('freddiefujiwara '));
        $this->assertFalse($ins -> validateForUserId('freddiefujiwara@'));
    }
    public function testCheckExistence(){
        $ins = new Invitation();
        $this->assertTrue(method_exists($ins,"checkExistence"));
    }
}
