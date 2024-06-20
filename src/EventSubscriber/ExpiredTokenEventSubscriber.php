<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ExpiredTokenEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationFailureEvent::class => 'onAuthenticationFailure',
        ];
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof ExpiredTokenException) {
            // Token JWT expiré
            $response = new JsonResponse(['message' => 'Token JWT expiré. Veuillez vous reconnecter.'], 401);
            $event->setResponse($response);
        }
    }
}