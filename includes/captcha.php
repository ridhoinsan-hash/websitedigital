<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateCaptchaCode($length = 5) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    $_SESSION['captcha_code'] = $code;
    $_SESSION['captcha_time'] = time();
    return $code;
}

function verifyCaptcha($input) {
    if (empty($_SESSION['captcha_code'])) {
        return false;
    }
    if (isset($_SESSION['captcha_time']) && (time() - $_SESSION['captcha_time']) > 600) {
        unset($_SESSION['captcha_code'], $_SESSION['captcha_time']);
        return false;
    }
    $ok = strtoupper(trim($input)) === strtoupper($_SESSION['captcha_code']);
    unset($_SESSION['captcha_code'], $_SESSION['captcha_time']);
    return $ok;
}

function generateMathCaptcha() {
    $a = random_int(1, 9);
    $b = random_int(1, 9);
    $_SESSION['captcha_math_answer'] = $a + $b;
    $_SESSION['captcha_math_time'] = time();
    return "$a + $b";
}

function verifyMathCaptcha($input) {
    if (!isset($_SESSION['captcha_math_answer'])) {
        return false;
    }
    if (isset($_SESSION['captcha_math_time']) && (time() - $_SESSION['captcha_math_time']) > 600) {
        unset($_SESSION['captcha_math_answer'], $_SESSION['captcha_math_time']);
        return false;
    }
    $ok = intval($input) === intval($_SESSION['captcha_math_answer']);
    unset($_SESSION['captcha_math_answer'], $_SESSION['captcha_math_time']);
    return $ok;
}
