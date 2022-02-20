<?php
namespace App\Questions\EventSubscriber;

use App\Core\Exception\NotFoundEntityException;
use App\Questions\Event\Question\BaseQuestionEvent;
use App\Questions\Event\Question\QuestionCreatedEvent;
use App\Questions\Event\Question\QuestionStatusChangedEvent;
use App\Questions\UseCase\Category\CategoryUpdateCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Подписчик для работы с обновлением счётчиков сущности Категория.
 */
class CategoryRefreshCountersSubscriber implements EventSubscriberInterface
{
    /**
     * @var CategoryUpdateCase Category Update Case
     */
    private CategoryUpdateCase $categoryUpdateCase;

    /**
     * Конструктор
     *
     * @param CategoryUpdateCase $categoryUpdateCase Category Update Case
     */
    public function __construct(
        CategoryUpdateCase $categoryUpdateCase,
    )
    {
        $this->categoryUpdateCase = $categoryUpdateCase;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            QuestionCreatedEvent::NAME => ['refreshCounters'],
            QuestionStatusChangedEvent::NAME => ['refreshCounters'],
        ];
    }

    /**
     * @param BaseQuestionEvent $event Question Event
     * @return void Обновляет считчики модели
     * @throws NotFoundEntityException
     */
    public function refreshCounters(BaseQuestionEvent $event): void
    {
        $category = $event->getQuestion()->getCategory();
        $this->categoryUpdateCase->updateCounters($category->getId());
    }
}
