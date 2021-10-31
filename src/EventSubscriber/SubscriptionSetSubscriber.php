<?php

namespace App\EventSubscriber;

use App\Events\SubscriptionSetEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SubscriptionSetSubscriber implements EventSubscriberInterface
{
    private ParameterBagInterface $parameterBag;
    private MailerInterface $mailer;

    public function __construct(ParameterBagInterface $parameterBag, MailerInterface $mailer)
    {
        $this->parameterBag = $parameterBag;
        $this->mailer = $mailer;
    }

    public function onSubscriptionSet(SubscriptionSetEvent $event)
    {
        $email = (new TemplatedEmail())
                ->from(new Address($this->parameterBag->get('admin_email'), $this->parameterBag->get('admin_name')))
                ->to($event->getUser()->getEmail())
                ->subject('Изменение подписки')
                ->htmlTemplate('dashboard/confirmation_subscription.html.twig')
        ;

        $this->mailer->send($email);
    }

    public static function getSubscribedEvents()
    {
        return [
            SubscriptionSetEvent::class => 'onSubscriptionSet'
        ];
    }
}