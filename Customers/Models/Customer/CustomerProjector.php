<?php
namespace Ntech\Customers\Models\Customer;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel;
use Ntech\Customers\Events\CustomerBasicInfoModified;
use Ntech\Customers\Events\CustomerEmailUpdated;
use Ntech\Customers\Events\CustomerLinkedToGatewayCustomer;
use Ntech\Customers\Events\CustomerRegistered;
use Ntech\Customers\Events\CustomerUnlinkedFromGatewayCustomer;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\EventSource\Domain\DomainEvent;

class CustomerProjector extends AbstractProjector
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
    }

    /**
     * Array of event class names that this projector needs to hook into
     * @return array
     */
    public function getEventsItProjects() : array
    {
        return [
            CustomerRegistered::class,
            CustomerBasicInfoModified::class,
            CustomerEmailUpdated::class
        ];
    }

    public function delete()
    {
        $this->connection->exec("TRUNCATE customers;");
    }

    public function projectCustomerRegistered(DomainEvent $domainEvent)
    {
        /** @var CustomerRegistered $event */
        $event = $domainEvent->getPayload();

        $qb = $this->entityManager->getRepository(CustomerDoctrineModel::class)->createQueryBuilder('customer');
        try {
            $qb->select('customer.countId')
                ->orderBy('customer.registeredOn', 'DESC')
                ->where('customer.company = :company')
                ->setMaxResults(1);
            $qb->setParameter(
                'company',
                $this->entityManager->getReference(CompanyDoctrineModel::class, $event->getCompanyId()->toString())
            );

            $currentCountNumber = $qb
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            $currentCountNumber = 0;
        }

        $stmt = $this->connection->prepare("
            INSERT INTO
            customers
            (id, company_id, name, registered_on, customer_since, count_id)
            VALUES
            (?,?,?,?,?,?)
        ");
        $stmt->execute([
            $event->getCustomerId(),
            $event->getCompanyId(),
            $event->getCustomerName(),
            $domainEvent->getRecordedOn()->toDateTimeString(),
            $event->getCustomerSince(),
            ($currentCountNumber + 1)
        ]);
    }

    public function projectCustomerBasicInfoModified(DomainEvent $domainEvent)
    {
        /** @var CustomerBasicInfoModified $event */
        $event = $domainEvent->getPayload();

        $stmt = $this->connection->prepare("
            UPDATE
            customers
            SET
            name = ?,
            customer_since = ?
            WHERE
            id = ?
        ");
        $stmt->execute([
            $event->getCustomerBasicInfo()->getName(),
            $event->getCustomerBasicInfo()->getCustomerSince()->toDateTimeString(),
            $event->getCustomerId()->toString()
        ]);
    }

    public function projectCustomerEmailUpdated(DomainEvent $domainEvent)
    {
        /** @var CustomerEmailUpdated $event */
        $event = $domainEvent->getPayload();

        $stmt = $this->connection->prepare("
            UPDATE
            customers
            SET
            email = ?
            WHERE
            id = ?
        ");
        $stmt->execute([
            $event->getNewEmail(),
            $event->getCustomerId()->toString()
        ]);
    }
}
