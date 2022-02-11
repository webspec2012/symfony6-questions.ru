<?php
namespace App\Users\Service;

use App\Core\Exception\AppException;

/**
 * Генератор случайного пароля заданной длинны
 */
final class PasswordGeneratorService
{
    /**
     * @var string Набор символов для формирования пароля
     */
    public static string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!-.[]?*()';

    /**
     * @param int $length Длинна
     * @return string Случайный пароль заданной длинны
     * @throws AppException В случае ошибки формирования
     */
    public static function generate(int $length = 10): string
    {
        try {
            $characterListLength = mb_strlen(PasswordGeneratorService::$characters, '8bit') - 1;

            $password = '';
            foreach(range(1, $length) as $i){
                $password .= PasswordGeneratorService::$characters[random_int(0, $characterListLength)];
            }

            return $password;
        } catch (\Throwable $e) {
            throw new AppException(sprintf("Ошибка при формировании случайного пароля: %s", $e->getMessage()), $e->getCode(), $e);
        }
    }
}
