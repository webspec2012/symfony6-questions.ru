<?php
namespace App\Questions\UseCase\Question;

use App\Questions\Dto\Question\QuestionCreateForm;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Questions\Entity\Question\Question;
use App\Questions\Entity\Question\QuestionInterface;
use App\Questions\Service\SlugGenerate\SlugGenerateInterface;
use App\Questions\UseCase\Category\CategoryFindCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Question Case: Создание нового вопроса
 */
final class QuestionCreateCase
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
     * @var QuestionSwitchStatusCase Question Switch Status Case
     */
    private QuestionSwitchStatusCase $questionSwitchStatusCase;

    /**
     * @var EntityManagerInterface Entity Manager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var WorkflowInterface Workflow Interface
     */
    private WorkflowInterface $questionStatusWorkflow;

    /**
     * @var SlugGenerateInterface Slug Generate
     */
    private SlugGenerateInterface $slugGenerate;

    /**
     * Конструктор сервиса
     *
     * @param CategoryFindCase $categoryFindCase Category Find Case
     * @param QuestionFindCase $questionFindCase Question Find Case
     * @param QuestionSwitchStatusCase $questionSwitchStatusCase Question Switch Status Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param WorkflowInterface $questionsQuestionStatusStateMachine Workflow Interface
     * @param SlugGenerateInterface $slugGenerate Slug Generate
     *
     * @return void
     */
    public function __construct(
        CategoryFindCase $categoryFindCase,
        QuestionFindCase $questionFindCase,
        QuestionSwitchStatusCase $questionSwitchStatusCase,
        EntityManagerInterface $entityManager,
        WorkflowInterface $questionsQuestionStatusStateMachine,
        SlugGenerateInterface $slugGenerate,
    )
    {
        $this->categoryFindCase = $categoryFindCase;
        $this->questionFindCase = $questionFindCase;
        $this->questionSwitchStatusCase = $questionSwitchStatusCase;
        $this->entityManager = $entityManager;
        $this->questionStatusWorkflow = $questionsQuestionStatusStateMachine;
        $this->slugGenerate = $slugGenerate;
    }

    /**
     * Создание нового вопроса
     *
     * @param QuestionCreateForm $form DTO с данными вопроса
     * @return Question Созданный вопрос
     * @throws ServiceException В случае ошибки
     */
    public function create(QuestionCreateForm $form): Question
    {
        ValidateDtoService::validateDto($form);

        $question = new Question();
        $question->setCategory($this->categoryFindCase->getCategoryById($form->category));
        $question->setTitle($form->title);
        $question->setText((string) $form->text);
        $question->setSlug($this->slugGenerate->generate($form->title));
        $question->setStatus(QuestionInterface::STATUS_UNPUBLISHED);
        $this->questionStatusWorkflow->getMarking($question);

        try {
            // save to DB
            $this->entityManager->persist($question);
            $this->entityManager->flush();

            // publish
            $this->questionSwitchStatusCase->publish($question->getId());
        } catch (\Throwable $e) {
            throw new ServiceException(
                message: $e->getMessage(),
                code: (int) $e->getCode(),
                previous: $e
            );
        }

        return $this->questionFindCase->getQuestionById($question->getId(), false);
    }
}
