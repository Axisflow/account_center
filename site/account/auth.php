<?php
!defined('DB_ACC_TYPE') && define('DB_ACC_TYPE', 'mysql');
!defined('DB_ACC_HOST') && define('DB_ACC_HOST', 'localhost');
!defined('DB_ACC_USER') && define('DB_ACC_USER', '');
!defined('DB_ACC_PASS') && define('DB_ACC_PASS', '');
!defined('DB_ACC_NAME') && define('DB_ACC_NAME', 'account');
!defined('DB_ACC_NOW_TABLE') && define('DB_ACC_NOW_TABLE', '0');

    function checkAccount(string $accountName, string $password) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);
        
        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `AccountName` = '$accountName'";
        $result = $db->query($cmd);
        $row = $result->fetch();
        if (password_verify($password, $row['Password'])) {
            return true;
        } else {
            return false;
        }
    }

    function setToken(string $accountName, string $token) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);
        
        $cmd = "UPDATE `".DB_ACC_NOW_TABLE."` SET `Token` = '$token' WHERE `AccountName` = '$accountName'";
        $db->query($cmd);
    }

    function authToken(string $accountName, string $token) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);
        
        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `AccountName` = '$accountName'";
        $result = $db->query($cmd);
        $row = $result->fetch();
        if ($row['token'] == $token) {
            return true;
        } else {
            return false;
        }
    }

    function genToken(): string {
        $token = base64_encode(random_bytes(64));
        return $token;
    }

?>