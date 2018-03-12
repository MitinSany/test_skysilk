<?php

namespace app\controller;

use \app\Application as App;
use app\exception\JsonFormatException;
use app\helper\Csrf;
use app\helper\Form;
use \app\model\User;
use \app\exception\Exception;
use \app\helper\EmailSender;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Signup extends Controller
{

    public function getSignupView(RequestInterface $request, ResponseInterface $response, array $vars)
    {
        if (App::$app->getAuth()->isLoggedIn()) {
            return new RedirectResponse('.');
        }
        $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);
        $secret = $csrf->getSecret();
        $token = $csrf->getToken($secret);
        $response->getBody()->write(App::$app->render('signup/signup', ['csrfToken' => $token]));
        return $response;
    }

    public function postSignup(RequestInterface $request, ResponseInterface $response, array $vars)
    {
        $result = ['success' => true, 'location' => 'successsignup'];

        $csrf = new Csrf(App::$app->getConfig()['csrf_salt']);

        if (!Form::checkFields($request, ['email', 'password'])) {
            throw new JsonFormatException('Missing require field');
        } elseif (!$csrf->checkToken($request->getParsedBody()['csrfToken'])) {
            throw new JsonFormatException('Bad request');
        }

        try {
            $config = App::$app->getConfig();
            $emailSender = new EmailSender(
                $config['email_setting']['from'],
                isset($config['email_setting']['is_smtp']) ?? $config['email_setting']['is_smtp'],
                $config['email_setting']['host'] ?? $config['email_setting']['host'],
                $config['email_setting']['port'] ?? $config['email_setting']['port'],
                $config['email_setting']['user'] ?? $config['email_setting']['user'],
                $config['email_setting']['password'] ?? $config['email_setting']['password']
            );
            $user = new User($emailSender);
            $user->email = $request->getParsedBody()['email'];
            $user->password = $request->getParsedBody()['password'];
            $user->firstName = isset($request->getParsedBody()['firstname']) ? $request->getParsedBody()['firstname'] : null;
            $user->lastName = isset($request->getParsedBody()['firstname']) ? $request->getParsedBody()['lastname'] : null;
            $user->signupCode = sha1(time() . $user->email);

            $signupUrl = self::getSignupURL($request, $user->signupCode);
            $message = self::getSignupMessage($signupUrl);
            $user->register($message);
            unset($user);
        } catch (\Exception $e) {
            throw new JsonFormatException($e->getMessage(), $e->getCode());
        }
        $response = new JsonResponse($result);
        return $response;
    }

    protected function getSignupURL(RequestInterface $request, string $signupCode): string
    {
        $uri = $request->getUri();
        $protocol = $uri->getScheme();

        $host = $uri->getHost();
        $port = $uri->getPort() != 80 ? ':' . $uri->getPort() : '';

        $path = $uri->getPath() . '_confirm?signup_code=' . $signupCode;
        $result = "$protocol://$host$port$path";
        return $result;
    }

    static public function getSignupMessage(string $signupUrl)
    {
        return "Congradultions!<br/>"
            . "You registered in <a href='https://www.linkedin.com/in/mitinalexander/'>Alexander Mitin</a>"
            . " test project.<br/>For continue registration follow this link: <a href='{$signupUrl}'>{$signupUrl}</a>";
    }

    static public function getSignupConfirm(RequestInterface $request, ResponseInterface $response, array $vars)
    {
        if (isset($request->getQueryParams()['signup_code'])) {
            $signupCode = $request->getQueryParams()['signup_code'];
        } else {
            throw new Exception('Missing required parameter', 500);
        }

        $user = new User();
        $user->activate($signupCode);
        App::$app->getAuth()->login($user->id);
        return new RedirectResponse('.');
    }

    public function getSuccessSignupView(RequestInterface $request, ResponseInterface $response, array $vars)
    {
        if (App::$app->getAuth()->isLoggedIn()) {
            return new RedirectResponse('.');
        }
        $response->getBody()->write(App::$app->render('message',
            [
                'title' => 'Success',
                'message' => 'User successful registered. Please check you email for continue registration',
                'style' => 'success'
            ]));
        return $response;
    }
}