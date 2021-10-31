<?php

namespace App\EventSubscriber;

use App\Events\EmailChangedEvent;
use App\Security\EmailChangeVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class EmailChangedSubscriber implements EventSubscriberInterface
{
    private EmailChangeVerifier $emailVerifier;
    private ParameterBagInterface $parameterBag;

    public function __construct(EmailChangeVerifier $emailVerifier, ParameterBagInterface $parameterBag)
    {
        $this->emailVerifier = $emailVerifier;
        $this->parameterBag = $parameterBag;
    }

    public function onEmailChanged(EmailChangedEvent $event)
    {
        $this->emailVerifier->sendEmailConfirmation('app_dashboard_change_email', $event->getUser(),
            (new TemplatedEmail())
                ->from(new Address($this->parameterBag->get('admin_email'), $this->parameterBag->get('admin_name')))
                ->to($event->getUser()->getEmail())
                ->subject('Пожалуйста, подтвердите Ваш Email для изменения')
                ->htmlTemplate('dashboard/confirmation_email.html.twig')
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            EmailChangedEvent::class => 'onEmailChanged'
        ];
    }
}