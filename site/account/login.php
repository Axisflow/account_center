<?php
    header("Content-type:text/html;charset=UTF-8");
    define('APP_ROOT', "../");

    require_once 'auth.php';
    require_once APP_ROOT.'cookie.php';

    $AccountName=$_POST['accountname'];
    $Password=$_POST['password'];
    $AutoLogin=isset($_POST['autologin'])?1:0;
    $Token;
    $VerifyCode=$_POST['verifycode'];
    // $code=$_SESSION['code'];    //獲取服務器生成的驗證碼

    session_start();        //開啟會話一獲取到服務器端驗證碼

    if($VerifyCode != $_SESSION['Verification_Code']){
        echo "<script>alert('驗證碼錯誤');history.back();</script>";
        exit;
    }

    if(checkAccount($AccountName, $Password)){
        $Token = genToken();
        echo "<p>Success</p>";
        
        $_SESSION['AccountName'] = $AccountName;
        $_SESSION['Token'] = $Token;
        setToken($AccountName, $Token);

        if(canUseCookie('basic')) {
            setcookie('Token', $Token, time() + (3600 * 24 * 30), "/");
            setcookie('AccountName', $AccountName, time() + (3600 * 24 * 30), "/");
            setcookie('AutoLogin', $AutoLogin, time() + (3600 * 24 * 30), "/");
        }

        header('Location: '.APP_ROOT.'index.htm');
    } else {
        // header('WWW-Authenticate: Basic realm="Axisflow"');
        header('HTTP/1.0 401 Unauthorized');
        echo "<script>alert('帳號或密碼錯誤');history.go(-1);</script>";
    }
?>