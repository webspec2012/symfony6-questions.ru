<?php
namespace App\Questions\EventSubscriber;

use App\Questions\Entity\Category\CategoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

/**
 * Подписчик для работы с workflow status сущности Категория.
 */
class CategoryStatusWorkflowSubscriber implements EventSubscriberInterface
{
    /**
     * Проверка разрешения на действие "delete".
     * Запрещено удаление категории, если в ней есть вопросы.
     *
     * @param GuardEvent $event
     */
    public function guardDelete(GuardEvent $event): void
    {
        /* @var CategoryInterface $category */
        $category = $event->getSubject();
        if ($category->getTotalQuestions() > 0) {
            $event->setBlocked(true, "Невозможно удалить категорию, т.к. в ней содержатся вопросы.");
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.questions_category_status.guard.delete' => ['guardDelete'],
        ];
    }
}
