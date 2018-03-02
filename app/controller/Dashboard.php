<?php

namespace app\controller;

use \app\Application as App;
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

        App::$app->render('dashboard/template', [
            'site' => $_SERVER['HTTP_HOST'],
            'path' => $_SERVER['REQUEST_URI'] == '/' ? 'dashboard' : $_SERVER['REQUEST_URI'],
            'userData' => $userData
        ]);

    }

    public function postProfile()
    {
        $user = App::$app->getAuth()->getUser();
        $user->firstName = $_POST['firstName'];
        $user->lastName = $_POST['lastName'];
        $user->save();


        header('Location: profile#saved', true, 302);
    }

}