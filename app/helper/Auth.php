<?php

namespace app\helper;


class Auth
{
    protected $loggedInKey = 'loggedIn';
    protected $userId = 'userId';
    protected $user;

    public function __construct($user)
    {
        Session::init();
        $this->user = $user;
    }

    public function isLoggedIn()
    {
        Session::init();
        return Session::get($this->loggedInKey);
    }

    public function login(int $userId)
    {
        Session::set($this->loggedInKey, true);
        Session::set($this->userId, $userId);
    }

    public function logout()
    {
        Session::destroy();
    }

    public function getUser()
    {
        $userId = Session::get($this->userId);
        $this->user->loadById($userId);
        return $this->user;
    }
}