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
        private HttpClientInterface $httpClient,
        private TagAwareCacheInterface $cachePool,
        private TokenStorageInterface $tokenStorage
    )
    {
    }

    #[Route('api/meteo', name: 'getWeather', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $token = $this->tokenStorage->getToken();
        /** @var User $user */
        $user = $token->getUser();
        $postalCode = $user->getPostalCode();
        $idCache = "weather_data". $postalCode;

        try {
            $weatherData = $this->cachePool->get($idCache, function() use($postalCode){
                $url = sprintf('http://api.openweathermap.org/data/2.5/weather?zip=%s,FR&units=metric&appid=9e7d1c6f55a80b48d3da4f3c3b412ab9', $postalCode);
                $response = $this->httpClient->request('GET', $url);
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