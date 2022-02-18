<?php
namespace App\Questions\Dto\Category;

use App\Core\Dto\DtoInterface;
use App\Questions\Entity\Category\CategoryInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для редактирования категории
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class CategoryUpdateForm implements DtoInterface
{
    /**
     * Конструктор
     *
     * @param CategoryInterface|null $category Category Entity
     */
    public function __construct(?CategoryInterface $category = null)
    {
        if ($category) {
            $this->id = $category->getId();
            $this->title = $category->getTitle();
            $this->slug = $category->getSlug();
            $this->description = $category->getDescription();
        }
    }

    /**
     * @var int ID
     *
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    public int $id;

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
