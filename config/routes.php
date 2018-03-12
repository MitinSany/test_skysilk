<?php

return [
    ['GET', '/', ['\app\controller\Dashboard', 'getIndex']],
    ['GET', '/profile', ['\app\controller\Dashboard', 'getIndex']],
    ['POST', '/profile', ['\app\controller\Dashboard', 'postProfile']],

    ['GET', '/login', ['\app\controller\Login', 'getLoginView']],
    ['POST', '/login', ['\app\controller\Login', 'postLogin']],
    ['GET', '/logout', ['\app\controller\Login', 'getLogout']],

    ['POST', '/signup', ['\app\controller\Signup', 'postSignup']],
    ['GET', '/signup', ['\app\controller\Signup', 'getSignupView']],
    ['GET', '/signup_confirm', ['\app\controller\Signup', 'getSignupConfirm']],
    ['GET', '/successsignup', ['\app\controller\Signup', 'getSuccessSignupView']],
];
