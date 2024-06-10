<?php

declare(strict_types=1);

namespace App\Controller\Advice;

use App\Repository\AdviceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[AsController]
final class GetAllAdvices
{
    public function __construct(
        private AdviceRepository $adviceRepository,
        private SerializerInterface $serializer,
        private TagAwareCacheInterface $cachePool
    )
    {
    }

    #[Route('/api/conseil', name: 'getAllAdvices', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $currentMonth = (new \DateTime())->format('m');
        $idCache = "getAllAdvices-" . $currentMonth;

        $jsonAdvices = $this->cachePool->get($idCache, function() use ($currentMonth){
            $advices = $this->adviceRepository->findAll();
            $applicableAdvices = [];

            foreach ($advices as $advice){
                if (in_array($currentMonth, $advice->getMonths())){
                    $applicableAdvices[] = $advice;
                }
            }

            if (!empty($applicableAdvices)){
                return $this->serializer->serialize($applicableAdvices, 'json');
            }

            return null;
        });

        if ($jsonAdvices !== null){
            return new JsonResponse($jsonAdvices, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}

