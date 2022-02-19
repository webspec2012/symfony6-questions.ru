<?php
namespace App\Questions\EventSubscriber;

use App\Core\Exception\NotFoundEntityException;
use App\Questions\Entity\Question\QuestionInterface;
use App\Questions\Event\Question\BaseQuestionEvent;
use App\Questions\Event\Question\QuestionStatusChangedEvent;
use App\Questions\UseCase\Category\CategoryUpdateCase;
use App\Questions\UseCase\Question\QuestionFindCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Подписчик для работы с обновлением счётчиков сущности Категория.
 */
class CategoryRefreshCountersSubscriber implements EventSubscriberInterface
{
    /**
     * @var QuestionFindCase Question Find Case
     */
    private QuestionFindCase $questionFindCase;

    /**
     * @var CategoryUpdateCase Category Update Case
     */
    private CategoryUpdateCase $categoryUpdateCase;

    /**
     * Конструктор
     *
     * @param QuestionFindCase $questionFindCase Question Find Case
     * @param CategoryUpdateCase $categoryUpdateCase Category Update Case
     */
    public function __construct(
        QuestionFindCase $questionFindCase,
        CategoryUpdateCase $categoryUpdateCase,
    )
    {
        $this->questionFindCase = $questionFindCase;
        $this->categoryUpdateCase = $categoryUpdateCase;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
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

        $totalPublishedQuestions = $this->questionFindCase->countQuestionsByCategory($category->getId(), QuestionInterface::STATUS_PUBLISHED);
        $this->categoryUpdateCase->updateTotalPublishedQuestions($category->getId(), $totalPublishedQuestions);
    }
}
