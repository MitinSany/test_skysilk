<?php

namespace app\helper;


class Auth
{
    const loggedInKey = 'loggedIn';
    const userId = 'userId';
    const ipKey = 'up';
    protected $user;
    protected $ip;

    public function __construct($user)
    {
        Session::init();
        $this->user = $user;
    }

    public function isLoggedIn()
    {
        Session::init();
        return Session::get(self::loggedInKey) && Session::get(self::ipKey) ;
    }

    public function login(int $userId)
    {
        Session::set(self::loggedInKey, true);
        Session::set(self::userId, $userId);
        Session::set(self::ipKey, $_SERVER['REMOTE_ADDR']);
    }

    public function logout()
    {
        Session::destroy();
    }

    public function getUser()
    {
        $userId = Session::get(self::userId);
        $this->user->loadById($userId);
        return $this->user;
    }
}