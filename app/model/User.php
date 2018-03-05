<?php

namespace app\model;

use app\exception\Exception;

class User extends Model
{

    protected $table = 'users';

    const PASSWORD_ALGO = PASSWORD_BCRYPT;

    protected $emailSender;

    /**
     * @var int
     */
    public $id = null;

    /**
     * @var string
     */
    public $email = null;

    /**
     * @var string
     */
    public $password = null;

    /**
     * @var string
     */
    public $firstName = null;

    /**
     * @var string
     */
    public $lastName = null;

    /**
     * @var string
     */
    public $signupCode = null;

    /**
     * @var bool
     */
    public $activated = 0;

    public function __construct($emailSender = null)
    {
        if ($emailSender) {
            $this->emailSender = $emailSender;
        }
        parent::__construct();
    }

    public function getData() {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->id ? $this->password : $this->getHashPassword(),
            'firstName' => $this->firstName ? $this->firstName : null,
            'lastName' => $this->lastName ? $this->lastName : null,
            'signupCode' => $this->signupCode ? $this->signupCode : null,
            'activated' => $this->activated,
        ];
    }

    public function save()
    {
        if ($this->id) {
            $query = "UPDATE {$this->table} SET email=:email,password=:password,firstName=:firstName,lastName=:lastName,"
                . "signupCode=:signupCode,activated=:activated WHERE id=:id LIMIT 1";
        } else {
            $query = "INSERT INTO {$this->table} ('id','email','password','firstName','lastName', 'signupCode', 'activated') "
                . "VALUES (:id,:email,:password,:firstName,:lastName,:signupCode,:activated);";
        }

        $data = $this->getData();

        return $this->query($query, $data);
    }

    protected function sendSignup(string $content)
    {
        $this->emailSender->send($this->email, $content);
    }

    public function register(string $content)
    {
        $this->save();
        $this->sendSignup($content);
    }

    public function activate(string $signupCode)
    {
        $userData = $this->getAt($this->table, 'signupCode', $signupCode);

        if (count($userData) === 0) {
            throw new Exception('No user found with:<pre>signup_code=' . $signupCode . '</pre>');
        }

        $userData = $userData[0];

        if ($userData['activated'] == 1) {
            throw new Exception('User already activated with:<pre>signup_code=' . $signupCode . '</pre>');

        }
        $this->load($userData);
        $this->activated = true;
        $this->save();
    }

    public function authorize(string $password) {
        $data = $this->getAt($this->table, 'email',$this->email);
        if(!$data) {
            return false;
        }
        $this->load($data[0]);
        return password_verify($password, $this->password);
    }

    protected function getHashPassword(){
        return password_hash($this->password, self::PASSWORD_ALGO);
    }

}