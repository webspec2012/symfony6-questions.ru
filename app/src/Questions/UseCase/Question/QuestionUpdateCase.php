<?php
namespace App\Questions\UseCase\Question;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Questions\Dto\Question\QuestionUpdateForm;
use App\Questions\Entity\Answer\AnswerInterface;
use App\Questions\Repository\AnswerRepository;
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
     * @var AnswerRepository Answer Repository
     */
    private AnswerRepository $answerRepository;

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
     * @param AnswerRepository $answerRepository Answer Repository
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        CategoryFindCase $categoryFindCase,
        QuestionFindCase $questionFindCase,
        AnswerRepository $answerRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
    )
    {
        $this->categoryFindCase = $categoryFindCase;
        $this->questionFindCase = $questionFindCase;
        $this->answerRepository = $answerRepository;
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
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }

    /**
     * Обновить счётчики вопроса
     *
     * @param int $questionId ID вопроса
     * @return bool Результат выполнения операции
     * @throws NotFoundEntityException
     */
    public function updateCounters(int $questionId): bool
    {
        $question = $this->questionFindCase->getQuestionById($questionId, false);

        // totalAnswers
        $question->setTotalAnswers($this->answerRepository->countAnswersByQuestion($questionId, null));

        // totalPublishedAnswers
        $question->setTotalPublishedAnswers($this->answerRepository->countAnswersByQuestion($questionId, AnswerInterface::STATUS_PUBLISHED));

        try {
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }
}
