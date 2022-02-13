<?php
namespace App\Core\Service\Notification\EmailNotification;

/**
 * Интерфейс для одного e-mail адреса
 */
interface EmailAddressInterface
{
    /**
     * @return string Имя
     */
    public function getName(): string;

    /**
     * @return string E-mail
     */
    public function getAddress(): string;
}
