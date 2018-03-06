<?php

namespace app\controller;

use \app\Application as App;
use app\helper\Csrf;
use app\helper\Form;
use app\helper\Session;
use \app\model\User;
use \app\exception\Exception;
use \app\helper\EmailSender;

class Dashboard extends Controller
{

    public function getIndex()
    {
        if (!App::$app->getAuth()->isLoggedIn()) {
            header('Location: login', true, 302);
        }

        $userData = App::$app->getAuth()->getUser()->getData();
        $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);
        $secret = $csrf->getSecret();
        $token = $csrf->getToken($secret);

        App::$app->render('dashboard/template', [
            'site' => $_SERVER['HTTP_HOST'],
            'path' => $_SERVER['REQUEST_URI'] == '/' ? 'dashboard' : $_SERVER['REQUEST_URI'],
            'userData' => $userData,
            'csrfToken' => $token
        ]);
    }

    public function postProfile()
    {
        if(!Form::checkFields(['firstName', 'lastName', 'csrfToken'])) {
            App::$app->render('message', [
                'title' => 'Error',
                'message' => 'Missing require field',
                'style' => 'danger'
            ], 500);
            exit;
        }

        $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);
        if(!$csrf->checkToken($_POST['csrfToken'])) {
            App::$app->render('message', [
                'title' => 'Error',
                'message' => 'Bad request',
                'style' => 'danger'
            ], 500);
            exit;
        }
        $user = App::$app->getAuth()->getUser();
        $user->firstName = $_POST['firstName'];
        $user->lastName = $_POST['lastName'];
        $user->save();

        header('Location: profile#saved', true, 302);
    }

}