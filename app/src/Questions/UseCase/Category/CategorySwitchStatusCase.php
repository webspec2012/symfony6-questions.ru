<?php
namespace App\Questions\UseCase\Category;

use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Category Case: Изменение статуса категории
 */
final class CategorySwitchStatusCase
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
     * @var LoggerInterface Logger
     */
    private LoggerInterface $logger;

    /**
     * Конструктор сервиса
     *
     * @param CategoryFindCase $categoryFindCase Category Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param WorkflowInterface $questionsCategoryStatusStateMachine Workflow Interface
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        CategoryFindCase $categoryFindCase,
        EntityManagerInterface $entityManager,
        WorkflowInterface $questionsCategoryStatusStateMachine,
        LoggerInterface $logger,
    )
    {
        $this->categoryFindCase = $categoryFindCase;
        $this->entityManager = $entityManager;
        $this->categoryStatusWorkflow = $questionsCategoryStatusStateMachine;
        $this->logger = $logger;
    }

    /**
     * Публикация категории
     *
     * @param int $id ID категории
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function publish(int $id): bool
    {
        return $this->categoryStatusWorkflow($id, 'publish');
    }

    /**
     * Снятие с публикации категории
     *
     * @param int $id ID категории
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function unpublish(int $id): bool
    {
        return $this->categoryStatusWorkflow($id, 'unpublish');
    }

    /**
     * Удаление категории
     *
     * @param int $id ID категории
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function delete(int $id): bool
    {
        return $this->categoryStatusWorkflow($id, 'delete');
    }

    /**
     * Изменить статус категории.
     * Проходит через систему workflow.
     *
     * @param int $id ID Категории
     * @param string $action Действие
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    private function categoryStatusWorkflow(int $id, string $action): bool
    {
        $category = $this->categoryFindCase->getCategoryById($id, false);

        if (!$this->categoryStatusWorkflow->can($category, $action)) {
            throw new ServiceException(sprintf("Действие '%s' недоступно для данной категории. (workflow)", $action));
        }

        try {
            $this->categoryStatusWorkflow->apply($category, $action);
        } catch (\LogicException $e) {
            throw new ServiceException(sprintf("Произошла ошибка в процессе '%s'. Попробуйте позже.", $action));
        }

        try {
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }
}
