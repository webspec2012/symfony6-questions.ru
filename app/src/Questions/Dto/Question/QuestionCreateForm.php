<?php
namespace App\Questions\Dto\Question;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для создания вопроса
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class QuestionCreateForm implements DtoInterface
{
    /**
     * @var int ID категории
     *
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    public int $category;

    /**
     * @var string Заголовок
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min=1,
     *     max=200
     * )
     */
    public string $title;

    /**
     * @var string|null Текст
     *
     * @Assert\Type("string")
     * @Assert\Length(
     *     max=5000
     * )
     */
    public ?string $text = null;
}
