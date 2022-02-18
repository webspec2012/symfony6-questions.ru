<?php
namespace App\Questions\Dto\Answer;

use App\Core\Dto\DtoInterface;
use App\Questions\Entity\Answer\AnswerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для редактирования ответа
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AnswerUpdateForm implements DtoInterface
{
    /**
     * Конструктор
     *
     * @param AnswerInterface|null $answer Answer Entity
     */
    public function __construct(?AnswerInterface $answer = null)
    {
        if ($answer) {
            $this->id = $answer->getId();
            $this->text = $answer->getText();
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
     * @var string Текст ответа
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *     max=5000
     * )
     */
    public string $text;
}
