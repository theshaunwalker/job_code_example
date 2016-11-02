<?php
namespace Ntech\Customers;

use Ntech\Customers\Events\ContactAddedToCustomer;
use Ntech\Customers\Models\CustomerContactDoctrine\CustomerContactDoctrineModel;
use Ntech\Uuid\Uuid;
use NtechUtility\Serializer\Serializable;

class CustomerContact implements Serializable
{
    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var Uuid
     */
    private $customerId;
    /**
     * User set alias for easy referencing
     * i.e. "Marketing Manager", "Salesperson"
     * @var string
     */
    private $alias;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $phone;

    const PRIMARY_CONTACT = 1;
    const BILLING_CONTACT = 2;

    /**
     * CustomerContact constructor.
     * @param Uuid $id
     * @param Uuid $customerId
     * @param string $alias
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $phone
     */
    public function __construct(
        Uuid $id,
        Uuid $customerId,
        string $alias,
        string $firstName,
        string $lastName,
        string $email,
        string $phone
    ) {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->alias = $alias;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new static(
            Uuid::fromString($data['id']),
            Uuid::fromString($data['customerId']),
            $data['alias'],
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            $data['phone']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'id' => $this->id->toString(),
            'customerId' => $this->customerId->toString(),
            'alias' => $this->alias,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone
        ];
    }

    public static function fromDoctrineModel(CustomerContactDoctrineModel $doctrine)
    {
        return new self(
            $doctrine->getId(),
            $doctrine->getCustomer()->getId(),
            $doctrine->getAlias(),
            $doctrine->getFirstName(),
            $doctrine->getLastName(),
            $doctrine->getEmail(),
            $doctrine->getPhone()
        );
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function getFullName()
    {
        return $this->firstName . " " . $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
