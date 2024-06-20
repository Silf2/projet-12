<?php

namespace App\Controller\Advice;

use App\Entity\Advice;
use App\Validator\ValidateArguments;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class PostAdvice
{

    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $em,
        private ValidateArguments $validateArguments
    ){
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/conseil', name: 'addAdvice', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $advice = $this->serializer->deserialize($request->getContent(), Advice::class, 'json', ['groups' => 'getAdvice']);
 
        $this->validateArguments->validateAndHandleErrors($advice);

        $this->em->persist($advice);
        $this->em->flush();

        $jsonAdvice = $this->serializer->serialize($advice, 'json', ['groups' => 'getAdvice']);

        return new JsonResponse($jsonAdvice, Response::HTTP_CREATED, [], true);
    }
}