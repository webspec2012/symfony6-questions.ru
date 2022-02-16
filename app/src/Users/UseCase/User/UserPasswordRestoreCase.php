<?php
namespace App\Users\UseCase\User;

use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\Notification\EmailNotification\EmailAddress;
use App\Core\Service\Notification\EmailNotification\EmailMessage;
use App\Core\Service\Notification\EmailNotification\EmailNotificationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * User Case: Восстановление пароля
 */
final class UserPasswordRestoreCase
{
    /**
     * @var UserFindCase User Find Case
     */
    private UserFindCase $userFindCase;

    /**
     * @var EntityManagerInterface Entity Manager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var EmailNotificationInterface Email Notification
     */
    private EmailNotificationInterface $emailNotification;

    /**
     * @var UserChangePasswordCase User Change Password Case
     */
    private UserChangePasswordCase $userChangePasswordCase;

    /**
     * @var LoggerInterface Logger
     */
    private LoggerInterface $logger;

    /**
     * Конструктор сервиса
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param EmailNotificationInterface $emailNotification E-mail Notification
     * @param UserChangePasswordCase $userChangePasswordCase User Change Password Case
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        UserFindCase $userFindCase,
        EntityManagerInterface $entityManager,
        EmailNotificationInterface $emailNotification,
        UserChangePasswordCase $userChangePasswordCase,
        LoggerInterface $logger,
    )
    {
        $this->userFindCase = $userFindCase;
        $this->entityManager = $entityManager;
        $this->emailNotification = $emailNotification;
        $this->userChangePasswordCase = $userChangePasswordCase;
        $this->logger = $logger;
    }

    /**
     * Отправка письма с токеном подтверждения
     *
     * @param int $id ID пользователя
     * @return bool Результат отправки письма
     * @throws NotFoundEntityException|ServiceException
     */
    public function sendEmail(int $id): bool
    {
        $user = $this->userFindCase->getUserById($id);
        if ($user->isAdmin()) {
            throw new ServiceException("Для данного пользователя функционал недоступен.");
        }

        try {
            // формирование токена для подтверждения (срок действия токена - 5 дней)
            $token = $this->getRandomToken()."___".strtotime('+5 days');
            $user->setPasswordRestoreToken($token);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // отправка письма пользователю
            $message = (new EmailMessage())
                ->setTo(new EmailAddress($user->getEmail(), $user->getUsername()))
                ->setSubject('Восстановление пароля')
                ->setTemplate('user/password-restore-request')
                ->setContext(compact('user', 'token'))
            ;
            $this->emailNotification->send($message);

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }

    /**
     * @param string $token Token подтверждения
     * @return bool Процессинг восстановления пароля
     * @throws ServiceException В случае ошибки
     */
    public function handle(string $token): bool
    {
        $token = trim(strip_tags($token));
        if (empty($token)) {
            throw new ServiceException("Не указан 'token' для восстановления пароля.");
        }

        try {
            $user = $this->userFindCase->getUserByPasswordRestoreToken($token);
        } catch (NotFoundEntityException $e) {
            throw new ServiceException("Указан невалидный token для восстановления пароля.", $e->getCode(), $e->getPrevious());
        }

        list($tokenString, $tokenTime) = explode('___', $token);
        if (empty($tokenTime) || $tokenTime < time()) {
            $this->sendEmail($user->getId());

            throw new ServiceException("Прошёл срок действия указнного token. Мы выслали вам новое письмо, пройдите по ссылке из него.");
        }

        $user->setPasswordRestoreToken(null);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->userChangePasswordCase->generateNewPasswordAndSendToEmail($user->getId());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }

    /**
     * @return string Случайный токен
     * @throws \Exception
     */
    private function getRandomToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
