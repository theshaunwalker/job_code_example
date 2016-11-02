<?php
namespace Ntech\Customers\Models\CustomerAddresses;

use Doctrine\DBAL\Driver\Connection;
use Ntech\Customers\Events\AddressAddedToCustomer;
use Ntech\Customers\Events\AddressRemovedFromCustomer;
use Ntech\Customers\Events\CustomerSetBillingAddress;
use Ntech\Customers\Events\CustomerSetPrimaryAddress;
use Ntech\Customers\Events\CustomerSetShippingAddress;
use NtechUtility\Cqrs\ReadModel\AbstractProjector;
use NtechUtility\EventSource\Domain\DomainEvent;

class CustomerAddressesProjector extends AbstractProjector
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
            AddressAddedToCustomer::class,
            AddressRemovedFromCustomer::class,
            CustomerSetPrimaryAddress::class,
            CustomerSetShippingAddress::class,
            CustomerSetBillingAddress::class,
        ];
    }

    public function delete()
    {
        $this->connection->exec("TRUNCATE customer_addresses;");
    }

    public function projectAddressAddedToCustomer(DomainEvent $domainEvent)
    {
        /** @var AddressAddedToCustomer $event */
        $event = $domainEvent->getPayload();
        $stmt = $this->connection->prepare("
            INSERT INTO
            customer_addresses
            (id, customer_id, alias, name_number, street1, street2, city, county, country, postcode, is_primary, billing, shipping, added_at)
            VALUES
            (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $event->getAddressId(),
            $event->getCustomerId(),
            $event->getAddress()->getAlias(),
            $event->getAddress()->getNameNumber(),
            $event->getAddress()->getStreet1(),
            $event->getAddress()->getStreet2(),
            $event->getAddress()->getCity(),
            $event->getAddress()->getCounty(),
            $event->getAddress()->getCountry(),
            $event->getAddress()->getPostcode(),
            0,
            0,
            0,
            $domainEvent->getRecordedOn()->toDateTimeString()
        ]);
    }

    public function projectAddressRemovedFromCustomer(DomainEvent $domainEvent)
    {
        /** @var AddressRemovedToCustomer $event */
        $event = $domainEvent->getPayload();
        $stmt = $this->connection->prepare("
            DELETE FROM
            customer_addresses
            WHERE
            customer_id = ?
            AND
            id = ?
        ");
        $stmt->execute([
            $event->getCustomerId(),
            $event->getAddressId()
        ]);
    }

    public function projectCustomerSetPrimaryAddress(DomainEvent $domainEvent)
    {
        /** @var CustomerSetPrimaryAddress $event */
        $event = $domainEvent->getPayload();
        $this->connection->beginTransaction();
        // Remove current primary
        $stmt = $this->connection->prepare("
            UPDATE
            customer_addresses
            SET
            is_primary = ?
            WHERE
            customer_id = ?
        ");
        $stmt->execute([
            1,
            $event->getCustomerId()->toString()
        ]);
        // Set new primary
        $stmt = $this->connection->prepare("
            UPDATE
            customer_addresses
            SET
            is_primary = ?
            WHERE
            id = ?
        ");
        $stmt->execute([
            1,
            $event->getAddressId()->toString()
        ]);
        $this->connection->commit();
    }

    public function projectCustomerSetShippingAddress(DomainEvent $domainEvent)
    {
        /** @var CustomerSetShippingAddress $event */
        $event = $domainEvent->getPayload();
        $this->connection->beginTransaction();
        // Remove current shipping
        $stmt = $this->connection->prepare("
            UPDATE
            customer_addresses
            SET
            shipping = ?
            WHERE
            customer_id = ?
        ");
        $stmt->execute([
            1,
            $event->getCustomerId()->toString()
        ]);
        // Set new shipping
        $stmt = $this->connection->prepare("
            UPDATE
            customer_addresses
            SET
            shipping = ?
            WHERE
            id = ?
        ");
        $stmt->execute([
            1,
            $event->getAddressId()->toString()
        ]);
        $this->connection->commit();
    }

    public function projectCustomerSetBillingAddress(DomainEvent $domainEvent)
    {
        /** @var CustomerSetBillingAddress $event */
        $event = $domainEvent->getPayload();
        $this->connection->beginTransaction();
        // Remove current billing
        $stmt = $this->connection->prepare("
            UPDATE
            customer_addresses
            SET
            billing = ?
            WHERE
            customer_id = ?
        ");
        $stmt->execute([
            1,
            $event->getCustomerId()->toString()
        ]);
        // Set new billing
        $stmt = $this->connection->prepare("
            UPDATE
            customer_addresses
            SET
            billing = ?
            WHERE
            id = ?
        ");
        $stmt->execute([
            1,
            $event->getAddressId()->toString()
        ]);
        $this->connection->commit();
    }
}
