<?php

namespace App\Repository;

use App\Document\Post;
use App\Document\User;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

class PostRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPaginated(User $user, int $page = 1, ?int $limit = 10)
    {
        return $this->createQueryBuilder()
            ->field('author')->equals($user)
            ->limit($limit)
            ->skip(($page - 1) * $limit)
            ->sort('createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function countAll(User $user): int
    {
        return $this->createQueryBuilder()
            ->field('author')->equals($user)
            ->count()
            ->getQuery()
            ->execute();
    }
}
