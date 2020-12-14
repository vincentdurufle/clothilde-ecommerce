<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param array $params
     * @throws TransportExceptionInterface
     */
    public function sendEmail($params = []): void
    {
        $email = (new TemplatedEmail())
            ->from($params['from'])
            ->to($params['to'])
            ->addReplyTo($params['replyTo'])
            ->subject($params['subject'])
            ->htmlTemplate($params['template'])
            ->context($params['params'] ?? []);

        $this->mailer->send($email);
    }
}