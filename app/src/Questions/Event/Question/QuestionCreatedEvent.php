<?php
namespace App\Questions\Event\Question;

/**
 * Событие: Вопрос создан
 */
final class QuestionCreatedEvent extends BaseQuestionEvent
{
    /**
     * @const ID события
     */
    public const NAME = 'questions.question.created';
}
