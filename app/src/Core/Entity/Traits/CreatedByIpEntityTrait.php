<?php
namespace App\Core\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Обеспечивает работу с полем created_by_ip у Entity
 */
trait CreatedByIpEntityTrait
{
    /**
     * @var string|null Created By IP
     * @see https://stackoverflow.com/questions/1076714/max-length-for-client-ip-address
     *
     * @ORM\Column(
     *     type="string",
     *     length=45,
     *     nullable=true,
     * )
     */
    protected ?string $created_by_ip = null;

    /**
     * @return string IP адрес при создании сущности
     */
    public function getCreatedByIp(): string
    {
        return (string) $this->created_by_ip;
    }

    /**
     * Установить IP адрес
     *
     * @param string $ip IP адрес
     * @return static
     */
    public function setCreatedByIp(string $ip): static
    {
        $this->created_by_ip = $ip;

        return $this;
    }
}
