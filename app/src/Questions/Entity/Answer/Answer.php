<?php
namespace App\Questions\Entity\Answer;

use Doctrine\ORM\Mapping as ORM;
use App\Core\Entity\Traits\CreatedByIpEntityTrait;
use App\Questions\Entity\Question\QuestionInterface;
use App\Core\Entity\Traits\BlameableEntityTrait;
use App\Core\Entity\Traits\StatusesEntityTrait;
use App\Core\Entity\Traits\TimestampableEntityTrait;

/**
 * Сущность "Ответ".
 *
 * @psalm-suppress MissingConstructor
 *
 * @ORM\Table(
 *     name="`questions_answer`",
 *     indexes={
 *          @ORM\Index(name="questions_answer_status", columns={"status"}),
 *          @ORM\Index(name="questions_answer_created_by_ip", columns={"created_by_ip"}),
 *          @ORM\Index(name="questions_answer_text", columns={"text"}, flags={"fulltext"})
 *     }
 * )
 *
 * @ORM\Entity(
 *     repositoryClass="App\Questions\Repository\AnswerRepository",
 * )
 */
class Answer implements AnswerInterface
{
    use BlameableEntityTrait;
    use CreatedByIpEntityTrait;
    use TimestampableEntityTrait;
    use StatusesEntityTrait;

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
     *     targetEntity="App\Questions\Entity\Question\Question"
     * )
     * @ORM\JoinColumn(
     *     name="question_id",
     *     referencedColumnName="id",
     *     onDelete="CASCADE"
     * )
     */
    private QuestionInterface $question;

    /**
     * @ORM\Column(
     *     type="text",
     *     length=5000,
     *     nullable=false
     * )
     */
    private string $text;

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
    public function getQuestion(): QuestionInterface
    {
        return $this->question;
    }

    /**
     * Установить Вопрос
     *
     * @param QuestionInterface $question Вопрос
     * @return static
     */
    public function setQuestion(QuestionInterface $question): static
    {
        $this->question = $question;

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
