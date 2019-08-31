<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class Mailer
{
    public const FROM_ADDRESS = 'kafkiansky@webshake.ru';

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct(
        Swift_Mailer $mailer,
        Environment $environment

    )  {
        $this->mailer = $mailer;
        $this->twig = $environment;

    }

    /**
     * @param User $user
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendConfirmationMessage(User $user)
    {
        $messageBody = $this->twig->render('email-templates/confirmation.html.twig', [
            'user' => $user
        ]);

        $message = new Swift_Message();
        $message
            ->setSubject('Вы успешно прошли регистрацию!')
            ->setFrom(self::FROM_ADDRESS)
            ->setTo($user->getEmail())
            ->setBody($messageBody, 'text/html');

        $this->mailer->send($message);
    }
}