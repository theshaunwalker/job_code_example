<?php
namespace Ntech\Customers\Models\CustomerCreditBalance;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Ntech\Customers\Events\CreditAddedToCustomer;
use Ntech\Customers\Events\CreditDebitedFromCustomer;
use Ntech\Customers\Events\CustomerRegistered;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\Cqrs\ReadModel\Projector;
use NtechUtility\EventSource\Domain\DomainEvent;

class CustomerCreditBalanceProjector extends AbstractProjector implements Projector
{

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        Connection $connection,
        EntityManager $entityManager
    ) {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
    }
    /**
     * Array of event class names that this projector needs to hook into
     * @return array
     */
    public function getEventsItProjects() : array
    {
        return [
            CustomerRegistered::class,
            CreditAddedToCustomer::class,
            CreditDebitedFromCustomer::class,
        ];
    }
    
    public function delete()
    {
        $this->connection->exec("TRUNCATE customer_credit_balance");
    }

    public function projectCustomerRegistered(DomainEvent $domainEvent)
    {
        /** @var CustomerRegistered $event */
        $event = $domainEvent->getPayload();
        $stmt = $this->connection->prepare("
            INSERT INTO
            customer_credit_balance
            (customer_id, credit)
            VALUES
            (?,?)
        ");
        $stmt->execute([
            $event->getCustomerId()->toString(),
            0
        ]);
    }

    public function projectCreditAddedToCustomer(DomainEvent $domainEvent)
    {
        /** @var CreditAddedToCustomer $event */
        $event = $domainEvent->getPayload();

        $creditDoctrineModel = $this->entityManager->getRepository(CustomerCreditDoctrineModel::class)
            ->findBy(['customerId' => $event->getCustomerId()->toString()]);
        if (count($creditDoctrineModel) == 0) {
            throw new \Exception("There is no customer credit balance record");
        }
        $creditDoctrineModel = $creditDoctrineModel[0];
        $newCreditTotal = $creditDoctrineModel->getCredit()->getAmount() + $event->getAmount()->getAmount();
        $stmt = $this->connection->prepare("
            UPDATE
            customer_credit_balance
            SET
            credit = ?
            WHERE
            customer_id = ?
        ");
        $stmt->execute([
            $newCreditTotal,
            $event->getCustomerId()->toString()
        ]);
    }

    public function projectCreditDebitedFromCustomer(DomainEvent $domainEvent)
    {
        /** @var CreditDebitedFromCustomer $event */
        $event = $domainEvent->getPayload();

        $creditDoctrineModel = $this->entityManager->getRepository(CustomerCreditDoctrineModel::class)
            ->findBy(['customerId' => $event->getCustomerId()->toString()]);
        if (count($creditDoctrineModel) == 0) {
            throw new \Exception("There is no customer credit balance record");
        }
        $creditDoctrineModel = $creditDoctrineModel[0];
        $newCreditTotal = $creditDoctrineModel->getCredit()->getAmount() - $event->getAmount()->getAmount();
        $stmt = $this->connection->prepare("
            UPDATE
            customer_credit_balance
            SET
            credit = ?
            WHERE
            customer_id = ?
        ");
        $stmt->execute([
            $newCreditTotal,
            $event->getCustomerId()->toString()
        ]);
    }
}
