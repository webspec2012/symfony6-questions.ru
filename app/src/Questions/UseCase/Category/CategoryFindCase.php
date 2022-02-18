<?php
namespace App\Questions\UseCase\Category;

use App\Questions\Entity\Category\Category;
use App\Questions\Repository\CategoryRepository;
use App\Core\Exception\NotFoundEntityException;

/**
 * Category Case: Найти категорию по различным критериям
 */
final class CategoryFindCase
{
    /**
     * @var CategoryRepository Category Repository
     */
    private CategoryRepository $categoryRepository;

    /**
     * Конструктор сервиса
     *
     * @param CategoryRepository $categoryRepository Category Repository
     *
     * @return void
     */
    public function __construct(
        CategoryRepository $categoryRepository,
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param int $id ID категории
     * @param bool $isPublished Выборка только опубликованных категорий
     * @return Category Информация о категории
     * @throws NotFoundEntityException В случае если категория не найдена
     */
    public function getCategoryById(int $id, bool $isPublished = true): Category
    {
        $category = $this->categoryRepository->findOneById($id, $isPublished);
        if (empty($category)) {
            throw new NotFoundEntityException(sprintf("Категория с ID '%s' не найдена.", $id));
        }

        return $category;
    }

    /**
     * @param string $slug SLUG категории
     * @param bool $isPublished Выборка только опубликованных категорий
     * @return Category Информация о категории
     * @throws NotFoundEntityException В случае если категория не найдена
     */
    public function getCategoryBySlug(string $slug, bool $isPublished = true): Category
    {
        $slug = trim(mb_strtolower($slug));
        if (empty($slug)) {
            throw new NotFoundEntityException("Не указан slug категории.");
        }

        $category = $this->categoryRepository->findOneBySlug($slug, $isPublished);
        if (empty($category)) {
            throw new NotFoundEntityException(sprintf("Категория с SLUG '%s' не найдена.", $slug));
        }

        return $category;
    }
}
