<?php
namespace App\Core\Entity\Traits;

use App\Users\Entity\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Обеспечивает работу с полями created_by и updated_by у Entity
 */
trait BlameableEntityTrait
{
    /**
     * @var UserInterface|null
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Users\Entity\User"
     * )
     * @ORM\JoinColumn(
     *     name="created_by",
     *     referencedColumnName="id",
     *     onDelete="SET NULL"
     * )
     */
    protected ?UserInterface $created_by = null;

    /**
     * @var UserInterface|null
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Users\Entity\User"
     * )
     * @ORM\JoinColumn(
     *     name="updated_by",
     *     referencedColumnName="id",
     *     onDelete="SET NULL"
     * )
     */
    protected ?UserInterface $updated_by = null;

    /**
     * @return UserInterface|null
     */
    public function getCreatedBy(): ?UserInterface
    {
        return $this->created_by;
    }

    /**
     * @param UserInterface|null $created_by
     * @return static
     */
    public function setCreatedBy(?UserInterface $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    /**
     * @return UserInterface|null
     */
    public function getUpdatedBy(): ?UserInterface
    {
        return $this->updated_by;
    }

    /**
     * @param UserInterface|null $updated_by
     * @return static
     */
    public function setUpdatedBy(?UserInterface $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    /**
     * Установить автора создания и обновления сущности
     *
     * @param UserInterface|null $loggedUser
     * @return void
     */
    public function updatedBlameables(?UserInterface $loggedUser): void
    {
        $this->setUpdatedBy($loggedUser);

        if ($this->getCreatedBy() == null) {
            $this->setCreatedBy($loggedUser);
        }
    }
}
