<?php
namespace App\Users\Service\PasswordGenerate;

use App\Core\Exception\ServiceException;

/**
 * Простой вариант реализации формирования пароля
 */
final class SimplePasswordGenerate implements PasswordGenerateInterface
{
    /**
     * @var string Набор символов для формирования пароля
     */
    public static string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!-.[]?*()';

    /**
     * @var int Дефолтная длина пароля
     */
    private int $lengthDefault;

    /**
     * Конструктор
     *
     * @param int $lengthDefault Дефолтная длина пароля
     */
    public function __construct(
        int $lengthDefault = 8,
    )
    {
        $this->lengthDefault = $lengthDefault;
    }

    /**
     * @inheitdoc
     * @throws ServiceException
     */
    public function generate(?int $length = null): string
    {
        if ($length === null) {
            $length = $this->lengthDefault;
        }

        try {
            $characterListLength = mb_strlen(SimplePasswordGenerate::$characters, '8bit') - 1;

            $password = '';
            foreach(range(1, $length) as $_){
                $password .= SimplePasswordGenerate::$characters[random_int(0, $characterListLength)];
            }

            return $password;
        } catch (\Throwable $e) {
            throw new ServiceException(
                message: sprintf("Ошибка при формировании случайного пароля: %s", $e->getMessage()),
                code: (int) $e->getCode(),
                previous: $e
            );
        }
    }
}
