<?php
namespace App\Users\Dto\User;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для изменения пароля пользователю
 */
final class UserChangePasswordForm implements DtoInterface
{
    /**
     * @var int ID пользователя
     *
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    public int $id;

    /**
     * @var string Пароль
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min=8,
     *     max=100
     * )
     */
    public string $password;
}
