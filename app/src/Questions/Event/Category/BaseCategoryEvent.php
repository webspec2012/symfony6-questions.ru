<?php
namespace App\Questions\Event\Category;

use App\Questions\Entity\Category\CategoryInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие связанное с категорией
 */
abstract class BaseCategoryEvent extends Event
{
    /**
     * @var CategoryInterface Category
     */
    private CategoryInterface $category;

    /**
     * Конструктор
     *
     * @param CategoryInterface $category Category
     */
    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }

    /**
     * @return CategoryInterface Category
     */
    public function getCategory(): CategoryInterface
    {
        return $this->category;
    }
}
