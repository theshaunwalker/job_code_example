<?php
namespace Ntech\Customers\Queries;

use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\Query;

class GetCustomerContactQuery implements Query
{
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * @var int
     */
    private $contactType;
    /**
     * @var bool
     */
    private $primaryFallback;

    /**
     * GetCustomerContactQuery constructor.
     * @param Uuid $customerId
     * @param int $contactType
     *      One of the CustomerContact constants, ie
     *      CustomerContact::PRIMARY_CONTACT, CustomerContact::BILLING_CONTACT
     * @param bool $primaryFallback
     *      Whether or not to return the primary contact if the specified
     *      contact type does not exist
     */
    public function __construct(
        Uuid $customerId,
        int $contactType,
        bool $primaryFallback = false
    ) {
        $this->customerId = $customerId;
        $this->contactType = $contactType;
        $this->primaryFallback = $primaryFallback;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return int
     */
    public function getContactType()
    {
        return $this->contactType;
    }

    /**
     * @return boolean
     */
    public function isPrimaryFallback()
    {
        return $this->primaryFallback;
    }
}
