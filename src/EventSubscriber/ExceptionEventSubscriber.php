<?php 

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use  Symfony\Component\Serializer\Exception\NotNormalizableValueException;

final class ExceptionEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ExceptionEvent::class => 'handleException'];
    }

    public function handleException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotNormalizableValueException) {
            $response = new JsonResponse(["message" => "Erreur dans les informations fournies."], 401);
            $event->setResponse($response);
        }
        elseif ($exception instanceof \InvalidArgumentException) {
            $response = new JsonResponse(["message" => $exception->getMessage()], 401);
            $event->setResponse($response);
        }
        elseif ($exception instanceof AuthenticationException) {
            $response = new JsonResponse(["message" => "L'utilisateur doit être connecté pour accéder à cette route."]);
            $event->setResponse($response);
        }
        elseif ($exception instanceof AccessDeniedHttpException) {
            $response = new JsonResponse(["message" => "Vous n'êtes pas autorisé à acceder à cette ressource."], 403);
            $event->setResponse($response);
        }
        elseif ($exception instanceof NotFoundHttpException) {
            $response = new JsonResponse(["message" => "La ressource demandée est introuvable."], 404);
            $event->setResponse($response);
        }
        elseif ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse(["message" => $exception->getMessage()]);
            $event->setResponse($response);
        } 
        else {
            $response = new JsonResponse(["message" => "Une erreure est survenue pendant le traitement de votre requête"], 500);
            $event->setResponse($response);
        }
    }
}