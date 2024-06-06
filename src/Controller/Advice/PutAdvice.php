<?php

namespace App\Controller\Advice;

use App\Entity\Advice;
use App\Repository\AdviceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class PutAdvice{

    public function __construct(
        private SerializerInterface $serializer,
        private AdviceRepository $adviceRepository,
        private EntityManagerInterface $em
    )
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('api/conseil/{id}', name: "updateAdvice", methods: ['PUT'])]
    public function __invoke(Request $request, Advice $currentAdvice): JsonResponse
    {        
        $updatedAdvice = $this->serializer->deserialize($request->getContent(), 
                Advice::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAdvice]);
        
        $this->em->persist($updatedAdvice);
        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}