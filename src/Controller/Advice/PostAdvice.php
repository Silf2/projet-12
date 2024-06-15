<?php

namespace App\Controller\Advice;

use App\Entity\Advice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AsController]
final class PostAdvice
{

    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ){
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/conseil', name: 'addAdvice', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $advice = $this->serializer->deserialize($request->getContent(), Advice::class, 'json');
        $months = $advice->getMonths();
    
        foreach ($months as $month) {
            if ($month < 1 || $month > 12) {
                throw new \InvalidArgumentException("Le mois $month n'est pas valide. Les mois doivent être des nombres entre 1 et 12.");
            }
        }

        if (count($months) !== count(array_unique($months))) {
            throw new \InvalidArgumentException("Vous avez spécifié plusieurs fois le même mois.");
        }

        $this->em->persist($advice);
        $this->em->flush();

        $jsonAdvice = $this->serializer->serialize($advice, 'json', ['groups' => 'getAdvice']);

        return new JsonResponse($jsonAdvice, Response::HTTP_CREATED, [], true);
    }
}