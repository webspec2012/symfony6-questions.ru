<?php
namespace App\Questions\Dto\Question;

use App\Core\Dto\DtoInterface;
use App\Questions\Entity\Question\Question;
use App\Questions\Entity\Question\QuestionInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для редактирования вопроса
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class QuestionUpdateForm implements DtoInterface
{
    /**
     * Конструктор
     *
     * @param QuestionInterface|null $question Question Entity
     */
    public function __construct(?QuestionInterface $question = null)
    {
        if ($question) {
            $this->id = $question->getId();
            $this->category = $question->getCategory()->getId();
            $this->title = $question->getTitle();
            $this->text = $question->getText();
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
