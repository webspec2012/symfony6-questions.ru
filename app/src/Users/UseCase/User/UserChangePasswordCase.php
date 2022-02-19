<?php
namespace App\Users\UseCase\User;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\Notification\EmailNotification\EmailAddress;
use App\Core\Service\Notification\EmailNotification\EmailMessage;
use App\Core\Service\Notification\EmailNotification\EmailNotificationInterface;
use App\Core\Service\ValidateDtoService;
use App\Users\Dto\User\UserChangePasswordForm;
use App\Users\Service\PasswordGenerate\PasswordGenerateInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * User Case: Изменение пароля пользователю
 */
final class UserChangePasswordCase
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
     * @var UserPasswordHasherInterface Password Encoder
     */
    private UserPasswordHasherInterface $passwordEncoder;

    /**
     * @var PasswordGenerateInterface Password Generate
     */
    private PasswordGenerateInterface $passwordGenerate;

    /**
     * @var EmailNotificationInterface Email Notification
     */
    private EmailNotificationInterface $emailNotification;

    /**
     * @var LoggerInterface Logger
     */
    private LoggerInterface $logger;

    /**
     * Конструктор сервиса
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param UserPasswordHasherInterface $passwordEncoder Password Encoder
     * @param PasswordGenerateInterface $passwordGenerate Password Generate
     * @param EmailNotificationInterface $emailNotification Email Notification
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        UserFindCase $userFindCase,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordEncoder,
        PasswordGenerateInterface $passwordGenerate,
        EmailNotificationInterface $emailNotification,
        LoggerInterface $logger,
    )
    {
        $this->userFindCase = $userFindCase;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->passwordGenerate = $passwordGenerate;
        $this->emailNotification = $emailNotification;
        $this->logger = $logger;
    }

    /**
     * Изменение пароля пользователю
     *
     * @param UserChangePasswordForm $form DTO с данными пользователя
     * @return bool Результат выполнения операции
     * @throws ServiceException|EntityValidationException|NotFoundEntityException
     */
    public function changePassword(UserChangePasswordForm $form): bool
    {
        ValidateDtoService::validateDto($form);

        $user = $this->userFindCase->getUserById($form->id);
        $user->setPlainPassword($form->password, $this->passwordEncoder);

        try {
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }

    /**
     * Сформировать новый случайный пароль и отправить на почту пользователю.
     *
     * @param int $userId ID пользователя
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function generateNewPasswordAndSendToEmail(int $userId): bool
    {
        $user = $this->userFindCase->getUserById($userId);

        // Установка нового пароля
        $password = $this->passwordGenerate->generate();
        $formData = new UserChangePasswordForm();
        $formData->id = $user->getId();
        $formData->password = $password;
        if (!$this->changePassword($formData)) {
            return false;
        }

        // Отправка пароля на почту
        try {
            $message = (new EmailMessage())
                ->setTo(new EmailAddress($user->getEmail(), $user->getUsername()))
                ->setSubject('Установлен новый пароль')
                ->setTemplate('user/set-new-password')
                ->setContext(compact('user', 'password'))
            ;

            $this->emailNotification->send($message);

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }
}
