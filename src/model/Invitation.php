<?php

class Invitation {
    public function issueAHash($length = 16){
        return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, $length);
    }
    public function validateForUserId($userId){
        return 1 == preg_match('/^[a-zA-Z0-9]+$/',$userId);
    }
    public function checkExistence($userId){
        return true;
    }
}

