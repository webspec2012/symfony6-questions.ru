<?php
namespace App\Core\Service\Notification\EmailNotification;

/**
 * Интерфейс для сервиса отправки e-mail уведомления
 */
interface EmailNotificationInterface
{
    /**
     * @param EmailMessageInterface $message Сообщение
     * @return void Отправка E-mail уведомления
     * @throws EmailNotificationException В случае ошибки
     */
    public function send(EmailMessageInterface $message): void;
}
