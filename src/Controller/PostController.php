<?php

namespace App\Controller;

use App\Document\Post;
use App\Document\User;
use App\Repository\PostRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/posts', name: 'api_post')]
class PostController extends AbstractController
{

    public function __construct(private DocumentManager $dm, private SerializerInterface $serializer) {}

    #[Route('/adduser', name: 'api_login', methods: ['GET'])]
    public function createUser(UserPasswordHasherInterface $hasher): JsonResponse
    {
        $user = new User();
        $user->setName('toto');
        $user->setEmail('toto@test.com');
        $user->setPassword($hasher->hashPassword($user, '123456'));

        $this->dm->persist($user);
        $this->dm->flush();

        return new JsonResponse(['message' => 'Login successful']);
    }



    #[Route('/', name: '_list', methods: ['GET'])]
    public function index(Request $request, PostRepository $postRepository): JsonResponse
    {
        $page  = max(1, (int) $request->query->get('page', 1));
        $limit = max(10, (int) $request->query->get('limit', 10));

        $user = $this->getUser();
        $user = $this->dm->getRepository(User::class)->find(1);
        $posts = $postRepository->findPaginated($user, $page, $limit);
        $total = $postRepository->countAll([]);

        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'data' => $posts,
            ],
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['post:list']]
        );
    }

    #[Route('', name: '_create', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        try {
            /** @var Post $post */
            $post = $this->serializer->deserialize(
                $request->getContent(),
                Post::class,
                'json',
                ['groups' => ['post:write']]
            );
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid format: ' . $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        $user = $this->dm->getRepository(User::class)->find(1);
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $post->setOwner($user);

        $errors = $validator->validate($post);
        if (count($errors) > 0) {
            return $this->jsonViolation($errors);
        }

        $this->dm->persist($post);
        $this->dm->flush();

        return $this->json($post, JsonResponse::HTTP_CREATED, [], ['groups' => ['post:read']]);
    }

    #[Route('/{id}', name: '_get', methods: ['GET'])]
    public function show(Post $post): JsonResponse
    {
        return $this->json($post, JsonResponse::HTTP_OK, [], ['groups' => ['post:read']]);
    }

    #[Route('/{id}', name: '_update', methods: ['PUT'])]
    public function update(Post $post, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $this->serializer->deserialize(
            $request->getContent(),
            Post::class,
            'json',
            ['object_to_populate' => $post, 'groups' => ['post:write']]
        );

        $errors = $validator->validate($post);

        if (count($errors) > 0) {
            return $this->jsonViolation($errors);
        }

        $this->dm->flush();

        return $this->json(
            $post,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['post:read']]
        );
    }

    private function jsonViolation($errors, $code = JsonResponse::HTTP_BAD_REQUEST)
    {
        $formattedErrors = array_map(fn($violation) => [
            'field' => $violation->getPropertyPath(),
            'message' => $violation->getMessage(),
        ], iterator_to_array($errors));

        return $this->json(['errors' => $formattedErrors], $code);
    }
}
