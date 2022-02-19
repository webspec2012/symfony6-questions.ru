<?php
namespace App\Questions\Event\Category;

/**
 * Событие: Категория изменила статус
 */
final class CategoryStatusChangedEvent extends BaseCategoryEvent
{
    /**
     * @const ID события
     */
    public const NAME = 'questions.category.status.changed';
}
