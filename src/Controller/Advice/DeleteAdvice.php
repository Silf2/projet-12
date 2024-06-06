<?php

namespace App\Controller\Advice;

use App\Entity\Advice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
final class DeleteAdvice
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {}

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/conseil/{id}', name: "deleteAdvice", methods: ['DELETE'])]
    public function __invoke(Advice $advice): JsonResponse
    {
        $this->em->remove($advice);
        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}