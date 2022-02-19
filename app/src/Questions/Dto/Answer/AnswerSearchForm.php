<?php
namespace App\Questions\Dto\Answer;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для поиска с фильтрацией по ответам
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AnswerSearchForm implements DtoInterface
{
    /**
     * @var int|null ID
     */
    public ?int $id = null;

    /**
     * @var string|null Статус
     */
    public ?string $status = null;

    /**
     * @var int|null Вопрос
     */
    public ?int $question = null;

    /**
     * @var string|null Поисковой запрос
     */
    public ?string $query = null;

    /**
     * @var string|null Сортировка
     */
    public ?string $orderBy = null;

    /**
     * @return array Доступные варианты сортировки
     */
    public static function getAvailableOrderBy(): array
    {
        return [
            'u.id_DESC' => 'ID, DESC',
            'u.id_ASC' => 'ID, ASC',
        ];
    }
}
