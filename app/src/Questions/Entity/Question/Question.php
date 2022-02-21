<?php
namespace App\Questions\Entity\Question;

use Doctrine\ORM\Mapping as ORM;
use App\Core\Entity\Traits\CreatedByIpEntityTrait;
use App\Core\Entity\Traits\SluggableEntityTrait;
use App\Core\Entity\Traits\BlameableEntityTrait;
use App\Core\Entity\Traits\StatusesEntityTrait;
use App\Core\Entity\Traits\TimestampableEntityTrait;
use App\Questions\Entity\Category\CategoryInterface;

/**
 * Сущность "Вопрос".
 *
 * @psalm-suppress MissingConstructor
 *
 * @ORM\Table(
 *     name="`questions_question`",
 *     indexes={
 *          @ORM\Index(name="questions_question_status", columns={"status"}),
 *          @ORM\Index(name="questions_question_created_by_ip", columns={"created_by_ip"}),
 *          @ORM\Index(name="questions_question_title", columns={"title"}, flags={"fulltext"}),
 *          @ORM\Index(name="questions_question_text", columns={"text"}, flags={"fulltext"})
 *     }
 * )
 *
 * @ORM\Entity(
 *     repositoryClass="App\Questions\Repository\QuestionRepository",
 * )
 */
class Question implements QuestionInterface
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
     * @ORM\ManyToOne(
     *     targetEntity="App\Questions\Entity\Category\Category"
     * )
     * @ORM\JoinColumn(
     *     name="category_id",
     *     referencedColumnName="id",
     *     onDelete="SET NULL"
     * )
     */
    private CategoryInterface $category;

    /**
     * @var string Title
     *
     * @ORM\Column(
     *     type="string",
     *     length=250,
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
    private string $text;

    /**
     * @ORM\Column(
     *     type="integer",
     *     nullable=false
     * )
     */
    private int $total_answers = 0;

    /**
     * @ORM\Column(
     *     type="integer",
     *     nullable=false
     * )
     */
    private int $total_published_answers = 0;

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
    public function getCategory(): CategoryInterface
    {
        return $this->category;
    }

    /**
     * Установить Категория
     *
     * @param CategoryInterface $category Категория
     * @return static
     */
    public function setCategory(CategoryInterface $category): static
    {
        $this->category = $category;

        return $this;
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
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Установить Текст
     *
     * @param string $text Текст
     * @return static
     */
    public function setText(string $text): static
    {
        $this->text = trim(strip_tags($text));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalAnswers(): int
    {
        return $this->total_answers;
    }

    /**
     * Установить Общее количество ответов
     *
     * @param int $count Количество
     * @return static
     */
    public function setTotalAnswers(int $count): static
    {
        $this->total_answers = $count;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPublishedAnswers(): int
    {
        return $this->total_published_answers;
    }

    /**
     * Установить Количество опубликованных ответов
     *
     * @param int $count Количество
     * @return static
     */
    public function setTotalPublishedAnswers(int $count): static
    {
        $this->total_published_answers = $count;

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
