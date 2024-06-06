<?php

namespace App\Controller\Weather;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsController]
final class GetWeatherForTown{
    
    public function __construct(
        private HttpClientInterface $httpClient,
        private TagAwareCacheInterface $cachePool
    )
    {
    }

    #[Route('api/meteo/{postalCode}', name: 'getWeatherForTown', methods: ['GET'])]
    public function __invoke(string $postalCode): JsonResponse
    {
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