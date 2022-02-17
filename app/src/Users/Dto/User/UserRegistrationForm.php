<?php
namespace App\Users\Dto\User;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для регистрации пользователя
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class UserRegistrationForm  implements DtoInterface
{
    /**
     * @var string Имя
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min=1,
     *     max=50
     * )
     */
    public string $name;

    /**
     * @var string E-mail
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min=3,
     *     max=100
     *     )
     * @Assert\Email()
     */
    public string $email;

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
