<?php
namespace App\Core\EventSubscriber\Traits;

use App\Core\Entity\Traits\BlameableEntityTrait;
use App\Users\Entity\UserInterface;
use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

/**
 * Данный Event Subscriber обеспечивает заполнение полей created_by и updated_by сущностей,
 * к которым прикреплен BlameableEntityTrait.
 */
final class BlameableEntityTraitSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security Security
     */
    private Security $security;

    /**
     * Конструктор
     *
     * @param Security $security
     */
    public function __construct(
        Security $security,
    )
    {
        $this->security = $security;
    }

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
        if (!method_exists($entity, 'updatedBlameables')) {
            return false;
        }

        /* @var UserInterface|null $user */
        $user = $this->security->getUser();

        /* @var BlameableEntityTrait $entity */
        $entity->updatedBlameables($user);

        return true;
    }
}
