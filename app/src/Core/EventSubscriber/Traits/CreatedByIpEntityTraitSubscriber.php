<?php
namespace App\Core\EventSubscriber\Traits;

use App\Core\Entity\Traits\CreatedByIpEntityTrait;
use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Данный Event Subscriber обеспечивает заполнение полей created_by_ipсущностей,
 * к которым прикреплен CreatedByIpEntityTrait.
 */
final class CreatedByIpEntityTraitSubscriber implements EventSubscriberInterface
{
    /**
     * @var Request|null Main Request
     */
    private ?Request $request;

    /**
     * Конструктор
     *
     * @param RequestStack $requestStack Request Stack
     */
    public function __construct(
        RequestStack $requestStack,
    )
    {
        $this->request = $requestStack->getMainRequest();
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    /**
     * Действия при событии prePersist
     *
     * @param LifecycleEventArgs $eventArgs
     * @return void
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();
        if (!method_exists($entity, 'setCreatedByIp')) {
            /* @var CreatedByIpEntityTrait $entity */
            if ($this->request && !empty($this->request->getClientIp())) {
                $entity->setCreatedByIp($this->request->getClientIp());
            }
        }
    }
}
