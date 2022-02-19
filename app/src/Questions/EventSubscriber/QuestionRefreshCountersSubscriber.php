<?php
namespace App\Questions\EventSubscriber;

use App\Core\Exception\NotFoundEntityException;
use App\Questions\Entity\Answer\AnswerInterface;
use App\Questions\Event\Answer\AnswerStatusChangedEvent;
use App\Questions\Event\Answer\BaseAnswerEvent;
use App\Questions\UseCase\Answer\AnswerFindCase;
use App\Questions\UseCase\Question\QuestionUpdateCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Подписчик для работы с обновлением счётчиков сущности Вопрос.
 */
class QuestionRefreshCountersSubscriber implements EventSubscriberInterface
{
    /**
     * @var AnswerFindCase Answer Find Case
     */
    private AnswerFindCase $answerFindCase;

    /**
     * @var QuestionUpdateCase Question Update Case
     */
    private QuestionUpdateCase $questionUpdateCase;

    /**
     * Конструктор
     *
     * @param AnswerFindCase $answerFindCase Answer Find Case
     * @param QuestionUpdateCase $questionUpdateCase Question Update Case
     */
    public function __construct(
        AnswerFindCase $answerFindCase,
        QuestionUpdateCase $questionUpdateCase,
    )
    {
        $this->answerFindCase = $answerFindCase;
        $this->questionUpdateCase = $questionUpdateCase;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
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

        $totalPublishedAnswers = $this->answerFindCase->countAnswersByQuestion($question->getId(), AnswerInterface::STATUS_PUBLISHED);
        $this->questionUpdateCase->updateTotalPublishedAnswers($question->getId(), $totalPublishedAnswers);
    }
}
