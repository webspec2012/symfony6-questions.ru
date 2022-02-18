<?php
namespace App\Questions\UseCase\Question;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Questions\Dto\Question\QuestionUpdateForm;
use App\Questions\UseCase\Category\CategoryFindCase;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Question Case: Редактирование вопроса
 */
final class QuestionUpdateCase
{
    /**
     * @var CategoryFindCase Category Find Case
     */
    private CategoryFindCase $categoryFindCase;

    /**
     * @var QuestionFindCase Question Find Case
     */
    private QuestionFindCase $questionFindCase;

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
     * @param QuestionFindCase $questionFindCase Question Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        CategoryFindCase $categoryFindCase,
        QuestionFindCase $questionFindCase,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
    )
    {
        $this->categoryFindCase = $categoryFindCase;
        $this->questionFindCase = $questionFindCase;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Редактирование вопроса
     *
     * @param QuestionUpdateForm $form DTO с данными вопроса
     * @return bool Результат выполнения операции
     * @throws ServiceException|EntityValidationException|NotFoundEntityException
     */
    public function update(QuestionUpdateForm $form): bool
    {
        ValidateDtoService::validateDto($form);

        $question = $this->questionFindCase->getQuestionById($form->id, false);
        $question->setCategory($this->categoryFindCase->getCategoryById($form->category, false));
        $question->setTitle($form->title);
        $question->setText((string) $form->text);

        try {
            $this->entityManager->persist($question);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }
}
