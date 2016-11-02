<?php
namespace Ntech\Customers\Models\CustomerUser;

use Doctrine\ORM\EntityManager;
use Ntech\Customers\Events\UserAddedToCustomer;
use Ntech\Customers\Events\UserRemovedFromCustomer;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Users\Models\SingleDoctrine\UserDoctrineModel;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\EventSource\Domain\DomainEvent;

class CustomerUserProjector extends AbstractProjector
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * Array of event class names that this projector needs to hook into
     * @return array
     */
    public function getEventsItProjects() : array
    {
        return [
            UserAddedToCustomer::class,
            UserRemovedFromCustomer::class
        ];
    }

    /**
     * Logic for deleting all projected data from this projection
     */
    public function delete()
    {
        $this->entityManager->getConnection()->exec("TRUNCATE customer_user;");
    }

    public function projectUserAddedToCustomer(DomainEvent $domainEvent)
    {
        /** @var UserAddedToCustomer $event */
        $event = $domainEvent->getPayload();

        $customerUser = new CustomerUserModel(
            $this->entityManager->getReference(CustomerDoctrineModel::class, $event->getCustomerId()->toString()),
            $this->entityManager->getReference(UserDoctrineModel::class, $event->getUserId()->toString())
        );
        $this->entityManager->persist($customerUser);
        $this->entityManager->flush();
    }

    public function projectUserRemovedFromCustomer(DomainEvent $domainEvent)
    {
        /** @var UserRemovedFromCustomer $event */
        $event = $domainEvent->getPayload();

        $customerUser = $this->entityManager->getRepository(CustomerUserModel::class)
            ->createQueryBuilder('cu')
            ->where('cu.customer = :customerId')
            ->andWhere('cu.user = :userId')
            ->setParameters([
                'customerId' => $event->getCustomerId()->toString(),
                'userId' => $event->getUserId()->toString()
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $this->entityManager->remove($customerUser);
        $this->entityManager->flush();
    }
}
