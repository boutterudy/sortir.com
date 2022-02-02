<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    function sendEmail($sender, $receiver, $subject, $message){
        $email = (new Email())
            ->from($sender)
            ->to($receiver)
            ->subject($subject)
            ->text($message);

        $this->mailer->send($email);
    }
}