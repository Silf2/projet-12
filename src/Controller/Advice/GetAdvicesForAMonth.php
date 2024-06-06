<?php

namespace App\Controller\Advice;

use App\Repository\AdviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GetAdvicesForAMonth extends AbstractController
{
    private $adviceRepository;
    private $serializer;

    public function __construct(AdviceRepository $adviceRepository, SerializerInterface $serializer)
    {
        $this->adviceRepository = $adviceRepository;
        $this->serializer = $serializer;
    }

    #[Route('/api/advice/{month}', name: 'adviceOnAMonth', methods: ['GET'])]
    public function __invoke(int $month): JsonResponse
    {
        $advices = $this->adviceRepository->findAll();
        $applicableAdvices = [];

        foreach ($advices as $advice){
            if (in_array($month, $advice->getMonths())){
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

