<?php

$app->get('/i', function () {
    phpinfo();
});

$app->get('/', ['\app\controller\Dashboard', 'getIndex']);
$app->get('/profile', ['\app\controller\Dashboard', 'getIndex']);
$app->post('/profile', ['\app\controller\Dashboard', 'postProfile']);

$app->get('/login', ['\app\controller\Login', 'getLoginView']);
$app->post('/login', ['\app\controller\Login', 'postLogin']);
$app->get('/logout', ['\app\controller\Login', 'getLogout']);

$app->post('/signup', ['\app\controller\Signup', 'postSignup']);
$app->get('/signup', ['\app\controller\Signup', 'getSignupView']);
$app->get('/signup_confirm', ['\app\controller\Signup', 'getSignupConfirm']);
$app->get('/successsignup', ['\app\controller\Signup', 'getSuccessSignupView']);