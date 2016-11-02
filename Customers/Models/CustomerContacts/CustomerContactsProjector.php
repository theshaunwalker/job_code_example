<?php
namespace Ntech\Customers\Models\CustomerContacts;

use Doctrine\DBAL\Driver\Connection;
use Ntech\Customers\Events\ContactAddedToCustomer;
use Ntech\Customers\Events\ContactRemovedFromCustomer;
use Ntech\Customers\Events\ContactSetAsPrimary;
use Ntech\Customers\Events\CustomerContactUpdated;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\EventSource\Domain\DomainEvent;

class CustomerContactsProjector extends AbstractProjector
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Array of event class names that this projector needs to hook into
     * @return array
     */
    public function getEventsItProjects() : array
    {
        return [
            ContactAddedToCustomer::class,
            CustomerContactUpdated::class,
            ContactRemovedFromCustomer::class,
            ContactSetAsPrimary::class,
        ];
    }

    public function delete()
    {
        $this->connection->exec("TRUNCATE customer_contacts;");
    }

    public function projectContactAddedToCustomer(DomainEvent $domainEvent)
    {
        /** @var ContactAddedToCustomer $event */
        $event = $domainEvent->getPayload();
        $stmt = $this->connection->prepare("
            INSERT INTO
            customer_contacts
            (id, customer_id, alias, first_name, last_name, email, phone, is_primary, is_billing)
            VALUES
            (?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $event->getCustomerContact()->getId(),
            $event->getCustomerId(),
            $event->getCustomerContact()->getAlias(),
            $event->getCustomerContact()->getFirstName(),
            $event->getCustomerContact()->getLastName(),
            $event->getCustomerContact()->getEmail(),
            $event->getCustomerContact()->getPhone(),
            0,
            0
        ]);
    }

    public function projectCustomerContactUpdated(DomainEvent $domainEvent)
    {
        /** @var CustomerContactUpdated $event */
        $event = $domainEvent->getPayload();
        $stmt = $this->connection->prepare("
            UPDATE
                customer_contacts
            SET
                alias = ?,
                first_name = ?,
                last_name = ?,
                email = ?,
                phone = ?
            WHERE
                id = ?
            AND
                customer_id = ?
        ");
        $stmt->execute([
            $event->getCustomerContact()->getAlias(),
            $event->getCustomerContact()->getFirstName(),
            $event->getCustomerContact()->getLastName(),
            $event->getCustomerContact()->getEmail(),
            $event->getCustomerContact()->getPhone(),
            $event->getCustomerContact()->getId()->toString(),
            $event->getCustomerId()->toString()
        ]);
    }

    public function projectContactSetAsPrimary(DomainEvent $domainEvent)
    {
        /** @var ContactSetAsPrimary $event */
        $event = $domainEvent->getPayload();
        $this->connection->beginTransaction();
        try {
            // Set all of them as non-primary
            $stmt = $this->connection->prepare("
                UPDATE
                customer_contacts
                SET
                is_primary = ?
                WHERE
                customer_id = ?
            ");
            $stmt->execute([
                1,
                $event->getCustomerId()
            ]);
            $stmt = $this->connection->prepare("
                UPDATE
                customer_contacts
                SET
                is_primary = ?
                WHERE
                id = ?
            ");
            $stmt->execute([
                1,
                $event->getContactId()
            ]);
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
        $this->connection->commit();
    }

    public function projectContactRemovedFromCustomer(DomainEvent $domainEvent)
    {
        /** @var ContactRemovedFromCustomer $event */
        $event = $domainEvent->getPayload();
        $stmt = $this->connection->prepare("
            DELETE FROM
            customer_contacts
            WHERE
            customer_id = ?
            AND
            id = ?
        ");
        $stmt->execute([
            $event->getCustomerId()->toString(),
            $event->getContactId()->toString()
        ]);
    }
}
