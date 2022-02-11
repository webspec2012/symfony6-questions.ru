<?php
namespace App\Users\Service\PasswordGenerate;

use App\Core\Exception\ServiceException;

/**
 * Интерфейс для сервиса генерации паролей
 */
interface PasswordGenerateInterface
{
    /**
     * @param int|null $length Длина пароля (если null - длина по-умолчанию)
     * @return string Случайный пароль заданной длинны
     * @throws ServiceException В случае ошибки
     */
    public function generate(?int $length = null): string;
}
