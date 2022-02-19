<?php
namespace App\Questions\Event\Answer;

use App\Questions\Entity\Answer\AnswerInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие связанное с ответом
 */
abstract class BaseAnswerEvent extends Event
{
    /**
     * @var AnswerInterface Answer
     */
    private AnswerInterface $answer;

    /**
     * Конструктор
     *
     * @param AnswerInterface $answer Answer
     */
    public function __construct(AnswerInterface $answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return AnswerInterface Answer
     */
    public function getAnswer(): AnswerInterface
    {
        return $this->answer;
    }
}
