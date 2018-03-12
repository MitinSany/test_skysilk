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
    protected $id = null;

    /**
     * @var string
     */
    protected $email = null;

    /**
     * @var string
     */
    protected $password = null;

    /**
     * @var string
     */
    protected $firstName = null;

    /**
     * @var string
     */
    protected $lastName = null;

    /**
     * @var string
     */
    protected $signupCode = null;

    /**
     * @var bool
     */
    protected $activated = 0;

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
            'password' => $this->password,
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
                . "signupCode=:signupCode,activated=:activated WHERE id=:id;";
        } else {
            $query = "INSERT INTO {$this->table} ('id','email','password','firstName','lastName', 'signupCode', 'activated')"
                . " VALUES (:id,:email,:password,:firstName,:lastName,:signupCode,:activated);";
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

    protected function getHashPassword(string $password){
        return password_hash($password, self::PASSWORD_ALGO);
    }

    public function setEmail(string $value)
    {
        $value = $this->filter($value);
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->email = $value;
        } else {
            throw new Exception("Entered address email is not a valid");
        }
    }

    public function setFirstName(string $value)
    {
        return $this->firstName = $this->filter($value);
    }

    public function setLastName(string $value)
    {
        return $this->lastName = $this->filter($value);
    }

    public function setPassword(string $value){
        return $this->password = $this->getHashPassword($value);
    }

}