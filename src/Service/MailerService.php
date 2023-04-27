<?php

namespace App\Service;


use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailerService
{
    private $replyTo;

    public function __construct(private MailerInterface $mailer, $replyTo) {
        $this->replyTo = $replyTo;
    }
    public function sendEmail(
        $receiver = 'emmanueldombwe@gmail.com',
        $subject = 'OBF Gestion Stagiaires Application Notification',
        $content = 'Sending emails is fun again!',
    ): void
    {
        echo 'hello ';
        $email = (new Email())
            ->from(new Address('obf.stagiaires@gmail.com'))
            ->to(new Address('emmanueldombwe@gmail.com'))
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>')
        ;
        try {dd($email);
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            
        }
    }

}