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
        $this->updateEntity($eventArgs->getEntity());
    }

    /**
     * Обновление сущности
     *
     * @param object $entity Обновляемая сущность
     * @return bool Сущность была обновлёна?
     */
    private function updateEntity(object $entity): bool
    {
        if (!method_exists($entity, 'setCreatedByIp')) {
            return false;
        }

        /* @var CreatedByIpEntityTrait $entity */
        $entity->setCreatedByIp($this->request->getClientIp());

        return true;
    }
}
