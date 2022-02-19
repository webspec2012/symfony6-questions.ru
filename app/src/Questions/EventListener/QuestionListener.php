<?php
namespace App\Questions\EventListener;

use App\Core\Service\FrontendUrlGenerator;
use App\Questions\Entity\Question\Question;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Модель событий для сущности "Вопрос"
 */
class QuestionListener
{
    /**
     * @var FrontendUrlGenerator Frontend Url Generator
     */
    private FrontendUrlGenerator $urlGenerator;

    /**
     * Конструктор
     *
     * @param FrontendUrlGenerator $urlGenerator Url Generator
     */
    public function __construct(
        FrontendUrlGenerator $urlGenerator,
    )
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Событие, которое вызвано после создания категории.
     *
     * @param Question $question
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(Question $question, LifecycleEventArgs $eventArgs): void
    {
        $question->setHref($this->generateQuestionHref($question->getId(), $question->getSlug()));

        $eventArgs->getObjectManager()->flush();
    }

    /**
     * Событие, которое вызвано до обновления категории.
     *
     * @param Question $question
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(Question $question, LifecycleEventArgs $eventArgs): void
    {
        $question->setHref($this->generateQuestionHref($question->getId(), $question->getSlug()));
    }

    /**
     * @param int $id Id вопроса
     * @param string $slug Slug вопроса
     * @return string Ссылка на вопрос
     */
    private function generateQuestionHref(int $id, string $slug): string
    {
        return $this->urlGenerator->getAbsolutePath('question_view', compact('id', 'slug'));
    }
}
