<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Validator\ValidateArguments;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class PostUser
{
    public function __construct(
        private SerializerInterface $serializer,
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidateArguments $validateArguments
    ) {
    }

    #[Route('/api/user', name: "createUser", methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json', ['groups' => 'postUser']);
        $user->setRoles(["ROLE_USER"]);
        $password = $user->getPassword();

        $this->validateArguments->validateAndHandleErrors($user);

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();

        $jsonUser = $this->serializer->serialize($user, 'json', ['groups' => 'getUser']);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }
}