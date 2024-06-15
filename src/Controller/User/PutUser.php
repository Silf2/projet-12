<?php

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class PutUser{

    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('api/user/{id}', name: "updateUser", methods: ['PUT'])]
    public function __invoke(Request $request, User $currentUser): JsonResponse
    {        
        $updatedUser = $this->serializer->deserialize($request->getContent(), 
                User::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser, 'groups' => 'postUser']);
            
        $password = $updatedUser->getPassword();
        $updatedUser->setPassword($this->passwordHasher->hashPassword($updatedUser, $password));
        $this->em->persist($updatedUser);
        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}