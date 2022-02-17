<?php
namespace App\Users\EventSubscriber\User;

use App\Users\Entity\UserInterface;
use App\Users\Event\User\UserCreatedEvent;
use App\Users\Event\User\UserEmailChangedEvent;
use App\Users\Message\User\UserEmailVerification;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Данный подписчик отвечает за отправку E-mail уведомлений с подтверждением E-mail адреса
 */
final class UserEmailVerificationSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface Message Bus
     */
    private MessageBusInterface $messageBus;

    /**
     * Конструктор
     *
     * @param MessageBusInterface $messageBus Message Bus
     */
    public function __construct(
        MessageBusInterface $messageBus
    )
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::NAME => 'onUserCreated',
            UserEmailChangedEvent::NAME => 'onUserEmailChanged',
        ];
    }

    /**
     * @param UserCreatedEvent $event
     */
    public function onUserCreated(UserCreatedEvent $event): void
    {
        $this->sendEmailVerification($event->getUser());
    }

    /**
     * @param UserEmailChangedEvent $event
     */
    public function onUserEmailChanged(UserEmailChangedEvent $event): void
    {
        $this->sendEmailVerification($event->getUser());
    }

    /**
     * @param UserInterface $user User
     * @return void Отправить письмо
     */
    private function sendEmailVerification(UserInterface $user): void
    {
        $this->messageBus->dispatch(new UserEmailVerification((int) $user->getId()));
    }
}
