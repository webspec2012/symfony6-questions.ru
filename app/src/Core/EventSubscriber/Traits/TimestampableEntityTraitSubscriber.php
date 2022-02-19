<?php
namespace App\Core\EventSubscriber\Traits;

use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Данный Event Subscriber обеспечивает заполнение полей created_at и updated_at сущностей,
 * к которым прикреплен TimestampableEntityTrait.
 */
final class TimestampableEntityTraitSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritdoc
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
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
     * Действия при событии preUpdate
     *
     * @param LifecycleEventArgs $eventArgs
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();
        if ($this->updateEntity($entity)) {
            $em = $eventArgs->getEntityManager();
            $em->getUnitOfWork()->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)), $entity);
        }
    }

    /**
     * Обновление сущности
     *
     * @param object $entity Обновляемая сущность
     * @return bool Сущность была обновлёна?
     */
    private function updateEntity(object $entity): bool
    {
        if (!method_exists($entity, 'updatedTimestamps')) {
            return false;
        }

        $entity->updatedTimestamps();
        return true;
    }
}
