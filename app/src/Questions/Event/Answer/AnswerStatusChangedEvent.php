<?php
namespace App\Questions\Event\Answer;

/**
 * Событие: Ответ изменил статус
 */
final class AnswerStatusChangedEvent extends BaseAnswerEvent
{
    /**
     * @const ID события
     */
    public const NAME = 'questions.answer.status.changed';
}
