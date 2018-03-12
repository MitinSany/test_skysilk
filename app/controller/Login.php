<?php

namespace app\controller;

use \app\Application as App;
use app\exception\Exception;
use app\exception\JsonFormatException;
use app\helper\Form;
use app\helper\Session;
use app\model\User;
use app\helper\Csrf;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use \Zend\Diactoros\Response\RedirectResponse;
use \Zend\Diactoros\Response\JsonResponse;

class Login extends Controller
{

    public function getLoginView(RequestInterface $request, ResponseInterface $response, array $vars)
    {
        $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);
        $secret = $csrf->getSecret();
        $token = $csrf->getToken($secret);

        if (App::$app->getAuth()->isLoggedIn()) {
            return new RedirectResponse('.');
        }
        $response->getBody()->write(App::$app->render('login/login', ['csrfToken' => $token]));
        return $response;
    }

    public function postLogin(RequestInterface $request, ResponseInterface $response, array $vars)
    {
        try {
            if (!Form::checkFields($request, ['login', 'password', 'csrfToken'])) {
                throw new JsonFormatException('Missing require field');
            }

            $user = new User();
            $user->email = $request->getParsedBody()['login'];
            $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);

            if (!$csrf->checkToken($request->getParsedBody()['csrfToken'])) {
                $result = ['success' => false, 'message' => 'Bad request'];
            } elseif ($user->authorize($request->getParsedBody()['password'])) {
                App::$app->getAuth()->login($user->id);
                $result = ['success' => true, 'location' => '.'];
            } else {
                $result = ['success' => false, 'message' => 'Login or password incorrect'];
            }
        } catch (Exception $e) {
            throw new JsonFormatException($e->getMessage(), $e->getCode());
        }
        $response = new JsonResponse($result);
        return $response;
    }

    public function getLogout(RequestInterface $request, ResponseInterface $response, array $vars)
    {
        Session::destroy();
        $response = new RedirectResponse('login');
        App::$app->emit($response);
    }

}