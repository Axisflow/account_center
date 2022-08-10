<?php
!defined('DB_ACC_TYPE') && define('DB_ACC_TYPE', 'mysql');
!defined('DB_ACC_HOST') && define('DB_ACC_HOST', 'localhost');
!defined('DB_ACC_USER') && define('DB_ACC_USER', '');
!defined('DB_ACC_PASS') && define('DB_ACC_PASS', '');
!defined('DB_ACC_NAME') && define('DB_ACC_NAME', 'account');
!defined('DB_ACC_NOW_TABLE') && define('DB_ACC_NOW_TABLE', '0');

!defined('APP_ROOT') && define('APP_ROOT', "../");

require_once 'auth.php';

if (session_status() == PHP_SESSION_NONE) session_start();

    function updateSessionAccountInfo() {
        if(!array_key_exists('AccountName', $_COOKIE) || !array_key_exists('Token', $_COOKIE)) {
            header('Location: '.APP_ROOT.'account/index.htm');
            exit;
        }

        $_SESSION['AccountName'] = $_COOKIE['AccountName'];
        $_SESSION['Token'] = $_COOKIE['Token'];
        $_SESSION['LastPlay'] = $_COOKIE['LastPlay'];

    }

    function getAccountName() {
        if(array_key_exists('AccountName', $_SESSION) && array_key_exists('Token', $_SESSION)) {
            if(authToken($_SESSION['AccountName'], $_SESSION['Token'])) {
                return $_SESSION['AccountName'];
            } else {
                header('Location: '.APP_ROOT.'account/index.htm');
                exit;
            }
        } else {
            updateSessionAccountInfo();
            return $_SESSION['AccountName'];
        }
    }

    function isLegalPassword(string $password) {
        if(strlen($password) < 8) {
            return "Password must be at least 8 characters long.";
        } else if(strlen($password) > 128) {
            return "Password must be at most 128 characters long.";
        } else if(!preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one uppercase letter.";
        } else if(!preg_match('/[a-z]/', $password)) {
            return "Password must contain at least one lowercase letter.";
        } else if(!preg_match('/[0-9]/', $password)) {
            return "Password must contain at least one number.";
        } /* else if(!preg_match('/[^A-Za-z0-9]/', $password)) {
            return "Password must contain at least one special character.";
        } */ else {
            return true;
        }
    }

    function isLegalAccountName(string $accountName) {
        if(strlen($accountName) < 4) {
            return "Account name must be at least 4 characters long.";
        } else if(strlen($accountName) > 32) {
            return "Account name must be at most 32 characters long.";
        } else if(!preg_match('/^[A-Za-z0-9_.&@-]+$/', $accountName)) {
            return "Account name must contain only letters, numbers and \"_ . & @ -\".";
        } else {
            return true;
        }
    }

    function createAccount($AccountName, $Password) {
        if(($errmsg = isLegalAccountName($AccountName)) !== true || ($errmsg = isLegalPassword($Password)) !== true) {
            return $errmsg;
        }

        $Password = password_hash($Password, PASSWORD_BCRYPT);

        $UUID = genUUID();
        $permission = 1;
        $timestamp = date('Y-m-d H:i:s');
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);
        
        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `AccountName` = '$AccountName'";
        $result = $db->query($cmd);
        if(!$result->fetch()) {
            $cmd = "INSERT INTO `".DB_ACC_NOW_TABLE."` (`UUID`, `AccountName`, `Password`, `CreateTime`, `Operator`) VALUES ('$UUID', '$AccountName', '$Password', '$timestamp', '$permission')";
            $db->query($cmd);
            return true;
        } else {
            return "Account Name already Exists!";
        }
    }

    function genUUID() {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $UUID = null;
        while(true) {
            $UUID = sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
                        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                        mt_rand(0, 0xffff),
                        mt_rand(0, 0x0fff) | 0x4000,
                        mt_rand(0, 0x3fff) | 0x8000,
                        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                    );
            
            $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `UUID` = '$UUID'";
            $result = $db->query($cmd);
            if(!$result->fetch()) {
                break;
            }
        }
        
        return $UUID;
    }

    function getAccountNameByUUID($UUID) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);
        
        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `UUID` = '$UUID'";
        $result = $db->query($cmd);
        $row = $result->fetch();
        return $row['AccountName'];
    }

    function getUUID($accountName) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `AccountName` = '".$accountName."'";
        $result = $db->query($cmd);
        $row = $result->fetch();
        return $row['UUID'];
    }

    function isLegalEMail(string $email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid E-Mail Address! Your E-Mail is not changed or unset.";
        } else {
            return true;
        }
    }

    function setEMail($UUID, $EMail) {
        if(($errmsg = isLegalEMail($EMail)) !== true) {
            return $errmsg;
        }
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $cmd = "UPDATE `".DB_ACC_NOW_TABLE."` SET `Email` = '$EMail' WHERE `UUID` = '$UUID'";
        $db->query($cmd);

        return true;
    }

    function isLegalDisplayName(string $displayName) {
        if(strlen($displayName) < 2) {
            return "Display Name must be at least 2 characters long.";
        } else if(strlen($displayName) > 60) {
            return "Display Name must be at most 60 characters long.";
        } else if(preg_match('/[#`]/', $displayName)) {
            return "Display Name must not contain \"# `\".";
        } else {
            return true;
        }
    }

    function setDisplayName($UUID, $DisplayName) {
        if(($errmsg = isLegalDisplayName($DisplayName)) !== true) {
            return $errmsg;
        }
        
        if(getDisplayName($UUID) == $DisplayName) {
            return true;
        }
        
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `DisplayName` = '$DisplayName'";
        $result = $db->query($cmd);
        $rows = $result->fetchAll();
        
        $SubIDs = array();
        $SubID = -1;
        for($i = 0; $i < count($rows); $i++) {
            $SubIDs[] = $rows[$i]['SubID'];
        }
        
        define('SUB_ID_MAX', 65535);
        for($i = 0; $i <= SUB_ID_MAX; $i++) {
            if(!in_array($i, $SubIDs)) {
                $SubID = $i;
                break;
            }
        }
        
        if($SubID == -1) {
            return "This User Name already was used too frequently! Your User Name is not changed or unset.";
        }
        
        $cmd = "UPDATE `".DB_ACC_NOW_TABLE."` SET `DisplayName` = '$DisplayName' WHERE `UUID` = '$UUID'";
        $db->query($cmd);
        
        $cmd = "UPDATE `".DB_ACC_NOW_TABLE."` SET `SubID` = '$SubID' WHERE `UUID` = '$UUID'";
        $db->query($cmd);

        return true;
    }

    function getDisplayName($UUID) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `UUID` = '$UUID'";
        $result = $db->query($cmd);
        $row = $result->fetch();
        return $row['DisplayName'];
    }

    function getSubID($UUID) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `UUID` = '$UUID'";
        $result = $db->query($cmd);
        $row = $result->fetch();
        return $row['SubID'];
    }

    function getLastPlay($accountName) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `AccountName` = '".$accountName."'";
        $result = $db->query($cmd);
        $row = $result->fetch();
        return $row['LastPlay'];
    }

    function setLastPlay($accountName, $playID) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $cmd = "UPDATE `".DB_ACC_NOW_TABLE."` SET `LastPlay` = '".$playID."' WHERE `AccountName` = '".$accountName."'";
        $db->query($cmd);
    }

    function getDisplayImage($UUID) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $cmd = "SELECT * FROM `".DB_ACC_NOW_TABLE."` WHERE `UUID` = '$UUID'";
        $result = $db->query($cmd);
        $row = $result->fetch();
        return $row['DisplayImage'];
    }

    function setDisplayImage($UUID, $DisplayImage) {
        $db = new PDO(DB_ACC_TYPE.':host='.DB_ACC_HOST.';dbname='.DB_ACC_NAME, DB_ACC_USER, DB_ACC_PASS);

        $cmd = "UPDATE `".DB_ACC_NOW_TABLE."` SET `DisplayImage` = '$DisplayImage' WHERE `UUID` = '$UUID'";
        $db->query($cmd);
    }
?>