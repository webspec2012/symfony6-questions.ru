<?php
namespace App\Questions\UseCase\Answer;

use App\Questions\Dto\Answer\AnswerCreateForm;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Questions\Entity\Answer\Answer;
use App\Questions\Entity\Answer\AnswerInterface;
use App\Questions\UseCase\Question\QuestionFindCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Answer Case: Создание нового ответа
 */
final class AnswerCreateCase
{
    /**
     * @var QuestionFindCase Question Find Case
     */
    private QuestionFindCase $questionFindCase;

    /**
     * @var AnswerFindCase Answer Find Case
     */
    private AnswerFindCase $answerFindCase;

    /**
     * @var AnswerSwitchStatusCase Answer Switch Status Case
     */
    private AnswerSwitchStatusCase $answerSwitchStatusCase;

    /**
     * @var EntityManagerInterface Entity Manager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var WorkflowInterface Workflow Interface
     */
    private WorkflowInterface $answerStatusWorkflow;

    /**
     * Конструктор сервиса
     *
     * @param QuestionFindCase $questionFindCase Question Find Case
     * @param AnswerFindCase $answerFindCase Answer Find Case
     * @param AnswerSwitchStatusCase $answerSwitchStatusCase Answer Switch Status Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param WorkflowInterface $questionsAnswerStatusStateMachine Workflow Interface
     *
     * @return void
     */
    public function __construct(
        QuestionFindCase $questionFindCase,
        AnswerFindCase $answerFindCase,
        AnswerSwitchStatusCase $answerSwitchStatusCase,
        EntityManagerInterface $entityManager,
        WorkflowInterface $questionsAnswerStatusStateMachine,
    )
    {
        $this->questionFindCase = $questionFindCase;
        $this->answerFindCase = $answerFindCase;
        $this->answerSwitchStatusCase = $answerSwitchStatusCase;
        $this->entityManager = $entityManager;
        $this->answerStatusWorkflow = $questionsAnswerStatusStateMachine;
    }

    /**
     * Создание нового ответа
     *ы
     * @param AnswerCreateForm $form DTO с данными ответа
     * @return Answer Созданный ответ
     * @throws ServiceException В случае ошибки
     */
    public function create(AnswerCreateForm $form): Answer
    {
        ValidateDtoService::validateDto($form);

        $answer = new Answer();
        $answer->setQuestion($this->questionFindCase->getQuestionById($form->question));
        $answer->setText($form->text);
        $answer->setStatus(AnswerInterface::STATUS_UNPUBLISHED);
        $this->answerStatusWorkflow->getMarking($answer);

        try {
            $this->entityManager->persist($answer);
            $this->entityManager->flush();

            // publish
            $this->answerSwitchStatusCase->publish($answer->getId());
        } catch (\Throwable $e) {
            throw new ServiceException(
                message: $e->getMessage(),
                code: (int) $e->getCode(),
                previous: $e
            );
        }

        return $this->answerFindCase->getAnswerById($answer->getId(), false);
    }
}
