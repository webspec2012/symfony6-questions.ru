<?php
namespace App\Questions\UseCase\Category;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Questions\Dto\Category\CategoryUpdateForm;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Category Case: Редактирование категории
 */
final class CategoryUpdateCase
{
    /**
     * @var CategoryFindCase Category Find Case
     */
    private CategoryFindCase $categoryFindCase;

    /**
     * @var EntityManagerInterface Entity Manager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var LoggerInterface Logger
     */
    private LoggerInterface $logger;

    /**
     * Конструктор сервиса
     *
     * @param CategoryFindCase $categoryFindCase Category Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        CategoryFindCase $categoryFindCase,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
    )
    {
        $this->categoryFindCase = $categoryFindCase;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Редактирование категории
     *
     * @param CategoryUpdateForm $form DTO с данными категории
     * @return bool Результат выполнения операции
     * @throws ServiceException|EntityValidationException|NotFoundEntityException
     */
    public function update(CategoryUpdateForm $form): bool
    {
        ValidateDtoService::validateDto($form);

        $category = $this->categoryFindCase->getCategoryById($form->id, false);

        $slug = trim(mb_strtolower($form->slug));
        if ($category->getSlug() !== $slug) {
            try {
                $this->categoryFindCase->getCategoryBySlug($slug, false);
                throw new ServiceException(sprintf("Slug '%s' уже используется другой категорией.", $slug));
            } catch (NotFoundEntityException $e) {}
        }

        $category->setTitle($form->title);
        $category->setSlug($form->slug);
        $category->setHref(sprintf("/category/%s/", $category->getSlug()));
        $category->setDescription((string) $form->description);

        try {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }
}
