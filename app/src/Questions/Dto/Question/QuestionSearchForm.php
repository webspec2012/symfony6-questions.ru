<?php
namespace App\Questions\Dto\Question;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для поиска с фильтрацией по вопросам
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class QuestionSearchForm implements DtoInterface
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
     * @var int|null Категория
     */
    public ?int $category = null;

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
