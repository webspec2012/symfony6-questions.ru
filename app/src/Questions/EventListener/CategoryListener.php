<?php
namespace App\Questions\EventListener;

use App\Core\Service\FrontendUrlGenerator;
use App\Questions\Entity\Category\Category;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Модель событий для сущности "Категория вопросов"
 */
class CategoryListener
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
     * @param Category $category
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(Category $category, LifecycleEventArgs $eventArgs): void
    {
        $category->setHref($this->generateCategoryHref($category->getSlug()));

        $eventArgs->getObjectManager()->flush();
    }

    /**
     * Событие, которое вызвано до обновления категории.
     *
     * @param Category $category
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(Category $category, LifecycleEventArgs $eventArgs): void
    {
        $category->setHref($this->generateCategoryHref($category->getSlug()));
    }

    /**
     * @param string $slug Slug категории
     * @return string Ссылка на категорию
     */
    private function generateCategoryHref(string $slug): string
    {
        return $this->urlGenerator->getAbsolutePath('question_category', ['category_slug' => $slug]);
    }
}
