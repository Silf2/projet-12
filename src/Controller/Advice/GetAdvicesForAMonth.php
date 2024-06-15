<?php

namespace App\Controller\Advice;

use App\Repository\AdviceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[AsController]
final class GetAdvicesForAMonth
{
    public function __construct(
        private AdviceRepository $adviceRepository, 
        private SerializerInterface $serializer,
        private TagAwareCacheInterface $cachePool)
    {
    }

    #[Route('/api/conseil/{month}', name: 'adviceOnAMonth', methods: ['GET'])]
    public function __invoke(int $month): JsonResponse
    {
        $idCache = "getAllAdvices-" . $month;

        $jsonAdvices = $this->cachePool->get($idCache, function() use ($month){
            $advices = $this->adviceRepository->findAll();
            $applicableAdvices = [];

            foreach ($advices as $advice){
                if (in_array($month, $advice->getMonths())){
                    $applicableAdvices[] = $advice;
                }
            }

            if (!empty($applicableAdvices)){
                return $this->serializer->serialize($applicableAdvices, 'json');
            }

            return null;
        });

        if ($jsonAdvices === null){
            throw new NotFoundHttpException("Le conseil n'existe pas.");
        }
        
        return new JsonResponse($jsonAdvices, Response::HTTP_OK, [], true);
    }
}
