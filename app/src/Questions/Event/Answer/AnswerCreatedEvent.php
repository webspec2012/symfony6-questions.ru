<?php
namespace App\Questions\Event\Answer;

/**
 * Событие: Ответ создан
 */
final class AnswerCreatedEvent extends BaseAnswerEvent
{
    /**
     * @const ID события
     */
    public const NAME = 'questions.answer.created';
}
