<?php
namespace App\Core\Entity\Traits;

use App\Core\Exception\EntityValidationException;
use Doctrine\ORM\Mapping as ORM;

/**
 * Обеспечивает работу с полем status у Entity
 */
trait StatusesEntityTrait
{
    /**
     * @var string Статус
     *
     * @ORM\Column(
     *     type="string",
     *     length=50,
     *     nullable=false
     * )
     */
    protected string $status = '';

    /**
     * @return string Статус
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Установить статус
     *
     * @param string $status Статус
     * @throws EntityValidationException
     */
    public function setStatus(string $status): self
    {
        if (!isset(static::getStatusList()[$status])) {
            throw new EntityValidationException(sprintf("Некорректный статус для пользователя: '%s'", $status));
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return string Получить статус в виде текста
     */
    public function getStatusAsText(): string
    {
        return static::getStatusList()[$this->status] ?? $this->getStatus();
    }

    /**
     * @return array Список возможных статусов
     */
    abstract public static function getStatusList(): array;
}
