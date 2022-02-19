<?php
namespace App\Questions\Dto\Category;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для поиска с фильтрацией по категориям
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class CategorySearchForm implements DtoInterface
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
     * @var string|null Название
     */
    public ?string $title = null;

    /**
     * @var string|null Slug
     */
    public ?string $slug = null;

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
            'u.title_DESC' => 'TITLE, DESC',
            'u.title_ASC' => 'TITLE, ASC',
        ];
    }
}
