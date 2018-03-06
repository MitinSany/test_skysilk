<?php

namespace app\controller;

use \app\Application as App;
use app\helper\Form;
use app\helper\Session;
use app\model\User;
use app\helper\Csrf;

class Login extends Controller
{

    public function getLoginView()
    {
        $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);
        $secret = $csrf->getSecret();
        $token = $csrf->getToken($secret);

        if (App::$app->getAuth()->isLoggedIn()) {
            header('Location: .', true, 302);
        }

        App::$app->render('login/login', ['csrfToken' => $token]);
    }

    public function postLogin()
    {
        header('Content-Type: application/json');

        $user = new User();
        $user->email = $_POST['login'];
        $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);

        if(Form::checkFields(['login', 'password', 'csrfToken'])) {
            $result = ['success' => false, 'message' => 'Missing require field'];
        } elseif(!$csrf->checkToken($_POST['csrfToken'])) {
            $result = ['success' => false, 'message' => 'Bad request'];
        } elseif($user->authorize($_POST['password'])){
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