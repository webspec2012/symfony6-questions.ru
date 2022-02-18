<?php
namespace App\Questions\Repository;

use App\Questions\Entity\Category\Category;
use App\Questions\Entity\Category\CategoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Category Entity
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @param int $id ID категории
     * @param bool $isPublished Опубликованная категория?
     * @return Category|null Найти категорию по ID
     */
    public function findOneById(int $id, bool $isPublished = true): ?Category
    {
        $criteria = $isPublished ? ['status' => CategoryInterface::STATUS_PUBLISHED] : [];
        $criteria['id'] = $id;

        return $this->findOneBy($criteria);
    }

    /**
     * @param string $slug Slug категории
     * @param bool $isPublished Опубликованная категория?
     * @return Category|null Найти категорию по Slug
     */
    public function findOneBySlug(string $slug, bool $isPublished = true): ?Category
    {
        $criteria = $isPublished ? ['status' => CategoryInterface::STATUS_PUBLISHED] : [];
        $criteria['slug'] = $slug;

        return $this->findOneBy($criteria);
    }
}
