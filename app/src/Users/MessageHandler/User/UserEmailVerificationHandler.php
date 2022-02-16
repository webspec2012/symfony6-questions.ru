<?php
namespace App\Users\MessageHandler\User;

use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Users\Message\User\UserEmailVerification;
use App\Users\UseCase\User\UserEmailVerificationCase;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Обработчик сообщения UserEmailVerification
 */
final class UserEmailVerificationHandler implements MessageHandlerInterface
{
    /**
     * @var UserEmailVerificationCase User Email Verification Case
     */
    private UserEmailVerificationCase $userEmailVerificationCase;

    /**
     * Конструктор
     *
     * @param UserEmailVerificationCase $userEmailVerificationCase User Email Verification Case
     */
    public function __construct(
        UserEmailVerificationCase $userEmailVerificationCase
    )
    {
        $this->userEmailVerificationCase = $userEmailVerificationCase;
    }

    /**
     * Отправка E-mail сообщения с кодом подтверждения
     *
     * @param UserEmailVerification $message Сообщение
     * @throws NotFoundEntityException|ServiceException
     */
    public function __invoke(UserEmailVerification $message)
    {
        $this->userEmailVerificationCase->sendEmail($message->getUserId());
    }
}
