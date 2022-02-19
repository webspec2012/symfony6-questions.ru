<?php
namespace App\Questions\UseCase\Answer;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Questions\Dto\Answer\AnswerUpdateForm;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Answer Case: Редактирование ответа
 */
final class AnswerUpdateCase
{
    /**
     * @var AnswerFindCase Answer Find Case
     */
    private AnswerFindCase $answerFindCase;

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
     * @param AnswerFindCase $answerFindCase Answer Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        AnswerFindCase $answerFindCase,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
    )
    {
        $this->answerFindCase = $answerFindCase;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Редактирование ответа
     *
     * @param AnswerUpdateForm $form DTO с данными ответа
     * @return bool Результат выполнения операции
     * @throws ServiceException|EntityValidationException|NotFoundEntityException
     */
    public function update(AnswerUpdateForm $form): bool
    {
        ValidateDtoService::validateDto($form);

        $answer = $this->answerFindCase->getAnswerById($form->id, false);
        $answer->setText($form->text);

        try {
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }
}
