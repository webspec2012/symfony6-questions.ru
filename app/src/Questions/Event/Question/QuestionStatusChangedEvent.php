<?php
namespace App\Questions\Event\Question;

/**
 * Событие: Вопрос изменил статус
 */
final class QuestionStatusChangedEvent extends BaseQuestionEvent
{
    /**
     * @const ID события
     */
    public const NAME = 'questions.question.status.changed';
}
