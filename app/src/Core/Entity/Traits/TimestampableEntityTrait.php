<?php
namespace App\Core\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Обеспечивает работу с полями created_at и updated_at у Entity
 */
trait TimestampableEntityTrait
{
    /**
     * @var DateTime|null
     *
     * @ORM\Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected ?DateTime $created_at = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected ?DateTime $updated_at = null;

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    /**
     * @param DateTime $created_at
     * @return static
     */
    public function setCreatedAt(DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param DateTime $updated_at
     * @return static
     */
    public function setUpdatedAt(DateTime $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Установить дату и время создания и обновления сущности
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new DateTime('now'));
        }
    }
}
