<?php
namespace App\Questions\Entity\Category;

use App\Core\Entity\Traits\CreatedByIpEntityTrait;
use App\Core\Entity\Traits\SluggableEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Entity\Traits\BlameableEntityTrait;
use App\Core\Entity\Traits\StatusesEntityTrait;
use App\Core\Entity\Traits\TimestampableEntityTrait;

/**
 * Сущность "Категория".
 *
 * @psalm-suppress MissingConstructor
 *
 * @ORM\Table(
 *     name="`questions_category`",
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(name="slug_unique", columns={"slug"})
 *     },
 *     indexes={
 *          @ORM\Index(name="questions_category_status", columns={"status"}),
 *          @ORM\Index(name="questions_category_created_by_ip", columns={"created_by_ip"})
 *     }
 * )
 *
 * @ORM\Entity(
 *     repositoryClass="App\Questions\Repository\CategoryRepository",
 * )
 */
class Category implements CategoryInterface
{
    use BlameableEntityTrait;
    use CreatedByIpEntityTrait;
    use TimestampableEntityTrait;
    use StatusesEntityTrait;
    use SluggableEntityTrait;

    /**
     * @var array<string, string> Список статусов
     */
    public static array $statusList = [
        self::STATUS_PUBLISHED => 'PUBLISHED',
        self::STATUS_UNPUBLISHED => 'UNPUBLISHED',
        self::STATUS_DELETED => 'DELETED',
    ];

    /**
     * @var int ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(
     *     type="integer",
     *     nullable=false,
     * )
     */
    private int $id;

    /**
     * @var string Title
     *
     * @ORM\Column(
     *     type="string",
     *     length=200,
     *     nullable=false,
     * )
     */
    private string $title;

    /**
     * @ORM\Column(
     *     type="text",
     *     length=5000,
     *     nullable=false
     * )
     */
    private string $description;

    /**
     * @ORM\Column(
     *     type="integer",
     *     nullable=false
     * )
     */
    private int $total_questions = 0;

    /**
     * @ORM\Column(
     *     type="integer",
     *     nullable=false
     * )
     */
    private int $total_published_questions = 0;

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Установить Название
     *
     * @param string $title Название
     * @return static
     */
    public function setTitle(string $title): static
    {
        $this->title = trim(strip_tags($title));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Установить Описание
     *
     * @param string $description Описание
     * @return static
     */
    public function setDescription(string $description): static
    {
        $this->description = trim(strip_tags($description));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalQuestions(): int
    {
        return $this->total_questions;
    }

    /**
     * Установить Общее количество вопросов
     *
     * @param int $count Количество
     * @return static
     */
    public function setTotalQuestions(int $count): static
    {
        $this->total_questions = $count;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPublishedQuestions(): int
    {
        return $this->total_published_questions;
    }

    /**
     * Установить Количество опубликованных вопросов
     *
     * @param int $count Количество
     * @return static
     */
    public function setTotalPublishedQuestions(int $count): static
    {
        $this->total_published_questions = $count;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPublished(): bool
    {
        return $this->getStatus() === self::STATUS_PUBLISHED;
    }

    /**
     * @inheritDoc
     */
    public function isDeleted(): bool
    {
        return $this->getStatus() === self::STATUS_DELETED;
    }

    /**
     * @inheritdoc
     */
    public static function getStatusList(): array
    {
        return self::$statusList;
    }
}
