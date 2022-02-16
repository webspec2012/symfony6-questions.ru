<?php
namespace App\Users\Dto\User;

use App\Core\Dto\DtoInterface;
use App\Users\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для редактирование профиля пользователя
 */
final class UserProfileUpdateForm implements DtoInterface
{
    /**
     * Конструктор
     *
     * @param User|null $user User Entity
     */
    public function __construct(?User $user = null)
    {
        if ($user) {
            $this->id = $user->getId();
            $this->name = $user->getUsername();
            $this->email = $user->getEmail();
            $this->about = $user->getAbout();
        }
    }

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
