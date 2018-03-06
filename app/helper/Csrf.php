<?php

namespace app\helper;

class Csrf
{
    /**
     * @var string
     */
    protected $salt;

    public function __construct(string $salt)
    {
        $this->salt = $salt;
    }

    public function getSecret()
    {
        return time();
    }

    public function getToken(string $secret)
    {
        return $secret . ':' . sha1($this->salt . $secret);
    }

    public function checkToken(string $token)
    {
        $secret = explode(':', $token)[0];
        $computedToken = $this->getToken($secret);
        return $token == $computedToken;
    }

}