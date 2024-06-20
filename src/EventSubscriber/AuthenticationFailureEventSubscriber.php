<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class AuthenticationFailureEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [LoginFailureEvent::class => 'handleLoginFailureResponse'];
    }

    public function handleLoginFailureResponse(LoginFailureEvent $event): void
    {
        $event->getResponse()->setContent(json_encode(['message' => "L'authentification de l'utilisateur a échoué."]));
    }
}
