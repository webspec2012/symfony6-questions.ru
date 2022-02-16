<?php
namespace App\Core\Service\Notification\EmailNotification;

/**
 * E-mail сообщение
 */
class EmailMessage implements EmailMessageInterface
{
    /**
     * @var EmailAddressInterface|null From
     */
    public ?EmailAddressInterface $from = null;

    /**
     * @var EmailAddressInterface[] To
     */
    public array $to = [];

    /**
     * @var string|null Subject
     */
    public ?string $subject = null;

    /**
     * @var string|null Template
     */
    public ?string $template = null;

    /**
     * @var array Context
     */
    public array $context = [];

    /**
     * @inheritDoc
     */
    public function getFrom(): ?EmailAddressInterface
    {
        return $this->from;
    }

    /**
     * @inheritDoc
     */
    public function setFrom(EmailAddressInterface $from): static
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * Установить Кому
     *
     * @param EmailAddressInterface[] $to To
     * @return static
     */
    public function setTo(EmailAddressInterface ...$to): static
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * Установить Тема
     *
     * @param string $subject Subject
     * @return static
     */
    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * Установить Шаблон
     *
     * @param string $template Template
     * @return static
     */
    public function setTemplate(string $template): static
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Установить Контекст
     *
     * @param array $context Context
     * @return static
     */
    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }
}
