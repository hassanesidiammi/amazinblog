<?php

namespace App\Controller;

use App\Controller\ViolationTrait as ControllerViolationTrait;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ViolationTrait;

#[Route('/api', name: 'api_user_profile')]
class UserController extends AbstractController
{
    use ControllerViolationTrait;

    public function __construct(private DocumentManager $dm, private SerializerInterface $serializer) {}

    #[Route('/profile', name: '_profile', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json($user, JsonResponse::HTTP_OK, [], ['groups' => ['user:read']]);
    }

    #[Route('/profile', name: '_update', methods: ['PUT'])]
    public function update(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $user = $this->getUser();
        $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            ['object_to_populate' => $user, 'groups' => ['user:write']]
        );

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return $this->jsonViolation($errors);
        }

        $this->dm->flush();

        return $this->json(
            $user,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['user:read']]
        );
    }
}
