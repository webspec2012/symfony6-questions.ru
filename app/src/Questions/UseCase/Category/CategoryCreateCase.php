<?php
namespace App\Questions\UseCase\Category;

use App\Questions\Dto\Category\CategoryCreateForm;
use App\Questions\Entity\Category\Category;
use App\Questions\Entity\Category\CategoryInterface;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Category Case: Создание новой категории
 */
final class CategoryCreateCase
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
     * @var WorkflowInterface Workflow Interface
     */
    private WorkflowInterface $categoryStatusWorkflow;

    /**
     * Конструктор сервиса
     *
     * @param CategoryFindCase $categoryFindCase Category Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param WorkflowInterface $questionsCategoryStatusStateMachine Workflow Interface
     *
     * @return void
     */
    public function __construct(
        CategoryFindCase $categoryFindCase,
        EntityManagerInterface $entityManager,
        WorkflowInterface $questionsCategoryStatusStateMachine,
    )
    {
        $this->categoryFindCase = $categoryFindCase;
        $this->entityManager = $entityManager;
        $this->categoryStatusWorkflow = $questionsCategoryStatusStateMachine;
    }

    /**
     * Создание новой категории
     *
     * @param CategoryCreateForm $form DTO с данными категории
     * @return Category Созданная категория
     * @throws ServiceException В случае ошибки
     */
    public function create(CategoryCreateForm $form): Category
    {
        ValidateDtoService::validateDto($form);

        try {
            $this->categoryFindCase->getCategoryBySlug($form->slug, false);
            throw new ServiceException(sprintf("Slug '%s' уже используется другой категорией.", $form->slug));
        } catch (NotFoundEntityException $e) {}

        $category = new Category();
        $category->setTitle($form->title);
        $category->setSlug($form->slug);
        $category->setDescription((string) $form->description);
        $category->setStatus(CategoryInterface::STATUS_UNPUBLISHED);
        $this->categoryStatusWorkflow->getMarking($category);

        try {
            $this->entityManager->persist($category);
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            throw new ServiceException(
                message: $e->getMessage(),
                code: (int) $e->getCode(),
                previous: $e
            );
        }

        return $this->categoryFindCase->getCategoryById($category->getId(), false);
    }
}
