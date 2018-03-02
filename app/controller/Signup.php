<?php

namespace app\controller;

use \app\Application as App;
use \app\model\User;
use \app\exception\Exception;
use \app\helper\EmailSender;

class Signup extends Controller
{

    public function getSignupView()
    {
        if (App::$app->getAuth()->isLoggedIn()) {
            header('Location: .', true, 302);
        }

        App::$app->render('signup/signup');
    }

    public function postSignup()
    {
        header('Content-Type: application/json');
        $result = ['success' => true, 'location' => 'successsignup'];
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
            $user->email = $_POST['email'];
            $user->password = $_POST['password'];
            $user->firstName = isset($_POST['firstname']) ? $_POST['firstname'] : null;
            $user->lastName = isset($_POST['firstname']) ? $_POST['lastname'] : null;
            $user->signupCode = sha1(time() . $user->email);

            $signupUrl = $this->getSignupURL($user->signupCode);
            $message = $this->getSignupMessage($signupUrl);
            $user->register($message);
            unset($user);
        } catch (\Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage()];
        }

        echo json_encode($result);
    }

    protected function getSignupURL(string $signupCode): string
    {
        $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        $host = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '';
        $path = $_SERVER['REQUEST_URI'] . '_confirm?signup_code=' . $signupCode;
        $result = "$protocol://$host$port$path";
        return $result;
    }

    protected function getSignupMessage(string $signupUrl)
    {
        return "Congradultions!<br/>"
            ."You registered in <a href='https://www.linkedin.com/in/mitinalexander/'>Alexander Mitin</a>"
            . " test project.<br/>For continue registration follow this link: <a href='{$signupUrl}'>{$signupUrl}</a>";
    }

    public function getSignupConfirm()
    {
        $signupCode = $_GET['signup_code'];
        try {
            $user = new User();
            $user->activate($signupCode);
            App::$app->getAuth()->login($user->id);
            header('Location: .', true, 302);
        } catch (Exception $e) {
            App::$app->render('message',
                ['title' => 'Token not found', 'message' => $e->getMessage(), 'style' => 'danger'], 404);
        }

    }

    public function getSuccessSignupView() {
        if(App::$app->getAuth()->isLoggedIn()) {
            header('Location: .', true, 302);
        }
        App::$app->render('message',
            [
                'title' => 'Success',
                'message' => 'User successful registered. Please check you email for continue registration',
                'style' => 'success'
            ]);
    }

}