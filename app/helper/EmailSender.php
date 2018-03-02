<?php

namespace app\helper;


use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailSender
{

    /**
     * @var PHPMailer
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    public function __construct(string $from, bool $isSmtp = false, string $host = '', int $port = 0, string $user = '', string $password = '')
    {
        $this->mailer = new PHPMailer;
        $this->mailer->From = $from;
        $this->mailer->setFrom($from, 'no-reply');
        if ($isSmtp) {
            $this->mailer->isSMTP();
            $this->mailer->Host = $host;
            $this->mailer->Port = $port;
            $this->mailer->Username = $user;
            $this->mailer->Password = $password;
            $this->mailer->SMTPAuth = true;
        } else {
            $this->mailer->isMail();
        }
        //$this->mailer->SMTPDebug = 1;
        $this->mailer->SMTPSecure = 'ssl';
        $this->from = $from;
    }

    public function send(string $email, string $content)
    {
        $this->mailer->isHTML(true);
        $this->mailer->addAddress($email);
        $this->mailer->Subject = 'Continue registration';
        $this->mailer->Body = $content;
        if (!$this->mailer->send()) {
            throw new Exception('Error due sending message: ' . $this->mailer->ErrorInfo);
        }
    }
}