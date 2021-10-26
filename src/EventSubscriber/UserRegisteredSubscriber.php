<?php

namespace App\EventSubscriber;

use App\Events\UserRegisteredEvent;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class UserRegisteredSubscriber implements EventSubscriberInterface
{
    private EmailVerifier $emailVerifier;
    private ParameterBagInterface $parameterBag;

    public function __construct(EmailVerifier $emailVerifier, ParameterBagInterface $parameterBag)
    {
        $this->emailVerifier = $emailVerifier;
        $this->parameterBag = $parameterBag;
    }

    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $event->getUser(),
            (new TemplatedEmail())
                ->from(new Address($this->parameterBag->get('admin_email'), $this->parameterBag->get('admin_name')))
                ->to($event->getUser()->getEmail())
                ->subject('Пожалуйста, подтвердите Ваш Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered'
        ];
    }
}