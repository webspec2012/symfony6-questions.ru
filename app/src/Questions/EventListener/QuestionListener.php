<?php
namespace App\Questions\EventListener;

use App\Core\Exception\ServiceException;
use App\Core\Service\FrontendUrlGenerator;
use App\Questions\Entity\Question\Question;
use App\Questions\Service\SlugGenerate\SlugGenerateInterface;
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
     * @var SlugGenerateInterface Slug Generate
     */
    private SlugGenerateInterface $slugGenerate;

    /**
     * Конструктор
     *
     * @param FrontendUrlGenerator $urlGenerator Url Generator
     * @param SlugGenerateInterface $slugGenerate Slug Generate
     */
    public function __construct(
        FrontendUrlGenerator $urlGenerator,
        SlugGenerateInterface $slugGenerate,
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->slugGenerate = $slugGenerate;
    }

    /**
     * Событие, которое вызвано до создания вопроса.
     *
     * @param Question $question
     * @param LifecycleEventArgs $eventArgs
     * @throws ServiceException
     */
    public function prePersist(Question $question, LifecycleEventArgs $eventArgs): void
    {
        $question->setSlug($this->slugGenerate->generate($question->getTitle()));
    }

    /**
     * Событие, которое вызвано после создания вопроса.
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
     * Событие, которое вызвано до обновления вопроса.
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
