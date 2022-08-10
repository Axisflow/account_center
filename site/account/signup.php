<?php
    header("Content-type:text/html;charset=UTF-8");
    define('APP_ROOT', "../");

    require_once 'check.php';

    $AccountName=$_POST['accountname'];
    $UserName=$_POST['username'];
    $Password=$_POST['password'];
    $Confirm_Password=$_POST['confirm_password'];
    $EMail = $_POST['email'];
    $VerifyCode=$_POST['verifycode'];

    if (session_status() == PHP_SESSION_NONE) session_start();

    if($VerifyCode != $_SESSION['Verification_Code']){
        echo "<script>alert('驗證碼錯誤');history.back();</script>";
        exit;
    }

    if($Password != $Confirm_Password){
        echo "<script>alert('確認密碼不一致');history.back();</script>";
        exit;
    }
    
    $error = 0;
    if(($errmsg = createAccount($AccountName, $Password)) !== true) {
        echo "<script>alert('".$errmsg."');history.back();</script>";
        $error++;
        exit;
    }

    if(($errmsg = setEMail(getUUID($AccountName), $EMail)) !== true) {
        echo "<script>alert('".$errmsg."');</script>";
        $error++;
    }

    if(($errmsg = setDisplayName(getUUID($AccountName), $UserName)) !== true) {
        echo "<script>alert('".$errmsg."');</script>";
        $error++;
    }

    if($error > 0) {
        /* echo "<script>history.go(-1);</script>";
        exit; */
    }

    echo "<script>alert('註冊成功，請重新登入');location.href='index.htm';</script>";
    
?>