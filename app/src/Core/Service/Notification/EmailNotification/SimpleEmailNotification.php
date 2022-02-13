<?php
namespace App\Core\Service\Notification\EmailNotification;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Простой сервис отправки e-mail уведомления (на основе mailer&twig)
 */
class SimpleEmailNotification implements EmailNotificationInterface
{
    /**
     * @var string $fromName От кого (имя)
     */
    private string $fromName;

    /**
     * @var string $fromEmail От кого (e-mail)
     */
    private string $fromEmail;

    /**
     * @var MailerInterface Mailer
     */
    private MailerInterface $mailer;

    /**
     * Конструктор
     *
     * @param string $fromName From Name
     * @param string $fromEmail From E-mail
     *
     * @param MailerInterface $mailer Mailer
     */
    public function __construct(
        string $fromName,
        string $fromEmail,

        MailerInterface $mailer,
    )
    {
        $this->fromName = $fromName;
        $this->fromEmail = $fromEmail;

        $this->mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    public function send(EmailMessageInterface $message): void
    {
        $message->setFrom(new EmailAddress($this->fromEmail, $this->fromName));

        if (empty($message->getTo())) {
            throw new EmailNotificationException("Не установлен параметр 'to'");
        }

        if (empty($message->getSubject())) {
            throw new EmailNotificationException("Не установлен параметр 'subject'");
        }

        if (empty($message->getTemplate())) {
            throw new EmailNotificationException("Не установлен параметр 'template'");
        }

        try {
            $tpl = (new TemplatedEmail())
                ->from(new Address($message->getFrom()->getAddress(), $message->getFrom()->getName()))
                ->to(...array_map(function (EmailAddressInterface $email) {
                    return new Address($email->getAddress(), $email->getName());
                }, $message->getTo()))
                ->subject($message->getSubject())
                ->htmlTemplate("mail/".$message->getTemplate().".html.twig")
                ->context($message->getContext())
            ;

            $this->mailer->send($tpl);
        } catch (\Throwable $e) {
            throw new EmailNotificationException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
}
