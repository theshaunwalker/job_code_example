<?php
namespace Ntech\Customers\Models\CustomerContactDoctrine;

use Doctrine\ORM\Mapping as ORM;
use Ntech\Uuid\Uuid;

/**
 * @ORM\Table(name="customer_contacts")
 * @ORM\Entity
 */
class CustomerContactDoctrineModel
{

    /**
     * @var Uuid
     *
     * @ORM\Column(name="id", type="guid", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel", inversedBy="contacts")
     */
    private $customer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_primary", type="boolean", nullable=false)
     */
    private $primary = false;

    /**
     * @var boolean
     * @ORM\Column(name="is_billing", type="boolean", nullable=false)
    **/
    private $billing = false;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=false)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;


    /**
     * @return Uuid
     */
    public function getId()
    {
        return Uuid::fromString($this->id);
    }

    /**
     * @return Uuid
     */
    public function getCustomer()
    {
        return $this->customer;
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

    /**
     * @return boolean
     */
    public function isPrimary()
    {
        return $this->primary;
    }

    /**
     * @return boolean
     */
    public function isBilling()
    {
        return $this->billing;
    }

}
