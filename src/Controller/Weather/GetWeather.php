<?php

namespace App\Controller\Weather;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[AsController]
final class GetWeather{
    
    public function __construct(
        private HttpClientInterface $weatherClient,
        private string $weatherRoute,
        private TagAwareCacheInterface $cachePool,
        private TokenStorageInterface $tokenStorage
    )
    {
    }

    #[Route('api/meteo/{postalCode?}', name: 'getWeatherForTown', methods: ['GET'])]
    public function __invoke(?string $postalCode = null): JsonResponse
    {
        if($postalCode === null){
            $token = $this->tokenStorage->getToken();
            /** @var User $user */
            $user = $token->getUser();
            $postalCode = $user->getPostalCode();
        }

        $idCache = "weather_data". $postalCode;

        try {
            $weatherData = $this->cachePool->get($idCache, function() use($postalCode){
                $url = sprintf($this->weatherRoute, $postalCode);
                $response = $this->weatherClient->request('GET', $url);
                $statusCode = $response->getStatusCode();
                
                if ($statusCode === 200) {
                    return $response->getContent();
                } else {
                    return new JsonResponse(['error' => 'Erreur dans la récupération des données météorologique'], $statusCode);
                }

            });
            return new JsonResponse($weatherData, 200, [], true);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}