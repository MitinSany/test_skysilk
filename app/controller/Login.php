<?php

namespace app\controller;

use \app\Application as App;
use app\helper\Session;
use app\model\User;

class Login extends Controller
{

    public function getLoginView()
    {
        if (App::$app->getAuth()->isLoggedIn()) {
            header('Location: .', true, 302);
        }

        App::$app->render('login/login');
    }

    public function postLogin()
    {
        header('Content-Type: application/json');
        $user = new User();
        $user->email = $_POST['login'];
        if($user->authorize($_POST['password'])){
            App::$app->getAuth()->login($user->id);
            $result = ['success' => true, 'location' => '.'];
        } else {
            $result = ['success' => false, 'message' => 'Login or password incorrect'];
        }
        echo json_encode($result);
    }

    public function getLogout() {
        Session::destroy();
        header('Location: login');
    }

}