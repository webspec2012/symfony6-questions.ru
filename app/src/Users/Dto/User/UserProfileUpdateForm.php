<?php
namespace App\Users\Dto\User;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для редактирование профиля пользователя
 */
final class UserProfileUpdateForm implements DtoInterface
{
    /**
     * @var int ID пользователя
     *
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    public int $id;

    /**
     * @var string Имя
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min=1,
     *     max=100
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
     * @var string|null О себе
     *
     * @Assert\Type("string")
     * @Assert\Length(
     *     max=1000
     * )
     */
    public ?string $about = null;
}
