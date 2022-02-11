<?php
namespace App\Users\Dto\User;

use App\Core\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для поиска с фильтрацией по пользователям
 */
final class UserSearchForm implements DtoInterface
{
    /**
     * @var int|null ID пользователя
     */
    public ?int $id = null;

    /**
     * @var string|null Статус
     */
    public ?string $status = null;

    /**
     * @var string|null Username
     */
    public ?string $name = null;

    /**
     * @var string|null E-mail
     */
    public ?string $email = null;

    /**
     * @var string|null Роль
     */
    public ?string $role = null;

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
