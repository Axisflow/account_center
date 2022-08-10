<?php

function canUseCookie(string $subPolicy = 'basic'): bool {
    $tmp = 'cookie_'.$subPolicy.'_policy';

    array_key_exists($tmp, $_COOKIE) ?: $_COOKIE[$tmp] = null;
    if($_COOKIE[$tmp] == 'agree') {
        return true;
    } else {
        return false;
    }
}

?>