<?php

declare(strict_types=1);

namespace App\Controller\Advice;

use App\Repository\AdviceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsController]
final class GetAllAdvices
{
    public function __construct(
        private AdviceRepository $adviceRepository,
        private SerializerInterface $serializer/*, 
        private HttpClientInterface $weatherClient*/
    )
    {
    }

    #[Route('/api/advices', name: 'getAllAdvices', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $advices = $this->adviceRepository->findAll();
        $jsonAdvices = $this->serializer->serialize($advices, 'json');

        return new JsonResponse($jsonAdvices, Response::HTTP_OK, [], true);
    }
}

