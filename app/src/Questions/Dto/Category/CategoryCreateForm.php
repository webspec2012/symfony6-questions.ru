<?php
namespace App\Questions\Dto\Category;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для создания категории
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class CategoryCreateForm implements DtoInterface
{
    /**
     * @var string Название
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
     * @var string Slug
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     min=3,
     *     max=100
     * )
     * @Assert\Regex(
     *     pattern="/^[-_\w]+$/"
     * )
     */
    public string $slug;

    /**
     * @var string|null Описание
     *
     * @Assert\Type("string")
     * @Assert\Length(
     *     max=5000
     * )
     */
    public ?string $description = null;
}
