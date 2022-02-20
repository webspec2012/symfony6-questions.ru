<?php
namespace App\Questions\EventSubscriber;

use App\Core\Exception\NotFoundEntityException;
use App\Questions\Event\Answer\AnswerCreatedEvent;
use App\Questions\Event\Answer\AnswerStatusChangedEvent;
use App\Questions\Event\Answer\BaseAnswerEvent;
use App\Questions\UseCase\Question\QuestionUpdateCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Подписчик для работы с обновлением счётчиков сущности Вопрос.
 */
class QuestionRefreshCountersSubscriber implements EventSubscriberInterface
{
    /**
     * @var QuestionUpdateCase Question Update Case
     */
    private QuestionUpdateCase $questionUpdateCase;

    /**
     * Конструктор
     *
     * @param QuestionUpdateCase $questionUpdateCase Question Update Case
     */
    public function __construct(
        QuestionUpdateCase $questionUpdateCase,
    )
    {
        $this->questionUpdateCase = $questionUpdateCase;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AnswerCreatedEvent::NAME => ['refreshCounters'],
            AnswerStatusChangedEvent::NAME => ['refreshCounters'],
        ];
    }

    /**
     * @param BaseAnswerEvent $event Answer Event
     * @return void Обновляет считчики модели
     * @throws NotFoundEntityException
     */
    public function refreshCounters(BaseAnswerEvent $event): void
    {
        $question = $event->getAnswer()->getQuestion();
        $this->questionUpdateCase->updateCounters($question->getId());
    }
}
