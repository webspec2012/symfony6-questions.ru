<?php
namespace App\Core\Service\Notification\EmailNotification;

/**
 * Интерфейс для одного e-mail сообщения
 */
interface EmailMessageInterface
{
    /**
     * @return EmailAddressInterface|null От кого
     */
    public function getFrom(): ?EmailAddressInterface;

    /**
     * Установить От кого
     *
     * @param EmailAddressInterface $from From
     * @return self
     */
    public function setFrom(EmailAddressInterface $from): self;

    /**
     * @return EmailAddressInterface[] Кому (список адресов)
     */
    public function getTo(): array;

    /**
     * @return string|null Тема сообщения
     */
    public function getSubject(): ?string;

    /**
     * @return string|null Название шаблона
     */
    public function getTemplate(): ?string;

    /**
     * @return array Контекст
     */
    public function getContext(): array;
}
