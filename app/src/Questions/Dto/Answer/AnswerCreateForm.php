<?php
namespace App\Questions\Dto\Answer;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для создания ответа
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AnswerCreateForm implements DtoInterface
{
    /**
     * @var int ID вопроса
     *
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    public int $question;

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
