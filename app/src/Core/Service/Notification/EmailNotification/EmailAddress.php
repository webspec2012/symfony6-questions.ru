<?php
namespace App\Core\Service\Notification\EmailNotification;

/**
 * E-mail адрес
 */
class EmailAddress implements EmailAddressInterface
{
    /**
     * @var string Name
     */
    private string $name;

    /**
     * @var string Address
     */
    private string $address;

    /**
     * Конструктор
     *
     * @param string $address Address
     * @param string $name Name
     */
    public function __construct(string $address, string $name = '')
    {
        $this->name = $name;
        $this->address = $address;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getAddress(): string
    {
        return $this->address;
    }
}
