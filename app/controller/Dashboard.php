<?php

namespace app\controller;

use \app\Application as App;
use app\helper\Csrf;
use app\helper\Form;
use app\helper\Session;
use \app\model\User;
use \app\exception\Exception;
use \app\helper\EmailSender;
use Zend\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Dashboard extends Controller
{

    public function __construct()
    {
        if (!App::$app->getAuth()->isLoggedIn()) {
            $response = new RedirectResponse('login');
            App::$app->emit($response);
        }
    }

    public function getIndex(RequestInterface $request, ResponseInterface $response, array $vars)
    {
        $userData = App::$app->getAuth()->getUser()->getData();
        $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);
        $secret = $csrf->getSecret();
        $token = $csrf->getToken($secret);
        $path = $request->getUri()->getPath();
        $site = $request->getUri()->getHost();
        $response->getBody()->write(App::$app->render('dashboard/template', [
            'site' => $site,
            'path' => $path == '/' ? 'dashboard' : $path,
            'userData' => $userData,
            'csrfToken' => $token
        ]));
        return $response;
    }

    public function postProfile(RequestInterface $request, ResponseInterface $response, array $vars)
    {
        if (!Form::checkFields($request, ['firstName', 'lastName', 'csrfToken'])) {
            throw new Exception('Missing require field');
        }

        $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);
        if (!$csrf->checkToken($request->getParsedBody()['csrfToken'])) {
            throw new Exception('Bad request');
        }
        $user = App::$app->getAuth()->getUser();
        $user->firstName = $request->getParsedBody()['firstName'];
        $user->lastName = $request->getParsedBody()['lastName'];
        $user->save();

        return new RedirectResponse('profile#saved');
    }
}