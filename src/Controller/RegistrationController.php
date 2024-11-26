<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $hasher, DocumentManager $dm, SerializerInterface $serializer): Response
    {
        $content = $request->getContent();
        try {
            /** @var User $user */
            $user = $serializer->deserialize(
                $content,
                User::class,
                'json',
                ['groups' => ['user:write']]
            );
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid format: ' . $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user->setPassword($hasher->hashPassword($user, json_decode($content, true)['password']));

        $dm->persist($user);
        $dm->flush();

        return $this->json($user, JsonResponse::HTTP_CREATED, [], ['groups' => ['user:read']]);
    }
}
