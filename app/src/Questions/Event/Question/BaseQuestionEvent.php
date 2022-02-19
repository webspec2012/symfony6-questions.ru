<?php
namespace App\Questions\Event\Question;

use App\Questions\Entity\Question\QuestionInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие связанное с вопросом
 */
abstract class BaseQuestionEvent extends Event
{
    /**
     * @var QuestionInterface Question
     */
    private QuestionInterface $question;

    /**
     * Конструктор
     *
     * @param QuestionInterface $question Question
     */
    public function __construct(QuestionInterface $question)
    {
        $this->question = $question;
    }

    /**
     * @return QuestionInterface Question
     */
    public function getQuestion(): QuestionInterface
    {
        return $this->question;
    }
}
