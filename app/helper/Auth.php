<?php

namespace app\helper;

use \Psr\Http\Message\RequestInterface;

class Auth
{
    const loggedInKey = 'loggedIn';
    const userId = 'userId';
    const ipKey = 'ip';
    protected $user;
    protected $ip;
    protected $request;


    public function __construct($user, RequestInterface $request)
    {
        Session::init();
        $this->user = $user;
        $this->request = $request;
    }

    public function isLoggedIn()
    {
        Session::init();
        return Session::get(self::loggedInKey) && Session::get(self::userId)
            && Session::get(self::ipKey) == $this->request->getServerParams()['REMOTE_ADDR'];
    }

    public function login(int $userId)
    {
        Session::set(self::loggedInKey, true);
        Session::set(self::userId, $userId);
        Session::set(self::ipKey, $this->request->getServerParams()['REMOTE_ADDR']);
    }

    public function logout()
    {
        Session::destroy();
    }

    public function getUser()
    {
        $userId = Session::get(self::userId);
        if ($userId) {
            $this->user->loadById($userId);
            return $this->user;
        } else {
            return false;
        }

    }
}