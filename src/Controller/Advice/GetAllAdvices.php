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

    #[Route('/api/conseil', name: 'getAllAdvices', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $advices = $this->adviceRepository->findAll();
        $currentMonth = (new \DateTime())->format('m');
        $applicableAdvices = [];

        foreach ($advices as $advice){
            if (in_array($currentMonth, $advice->getMonths())){
                $applicableAdvices[] = $advice;
            }
        }

        if (!empty($applicableAdvices)){
            $jsonAdvices = $this->serializer->serialize($applicableAdvices, 'json');
            return new JsonResponse($jsonAdvices, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}

