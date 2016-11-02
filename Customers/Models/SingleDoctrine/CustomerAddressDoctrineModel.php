<?php
namespace Ntech\Customers\Models\SingleDoctrine;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use NtechUtility\PhysicalAddress\IsAPostalAddress;
use NtechUtility\PhysicalAddress\PostalAddress;

/**
 * @ORM\Table(name="customer_addresses")
 * @ORM\Entity
 */
class CustomerAddressDoctrineModel implements IsAPostalAddress
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="alias", type="string")
     */
    protected $alias;

    /**
     * @var string
     * @ORM\Column(name="name_number", type="string", length=255, nullable=false)
     */
    protected $nameNumber;
    /**
     * @var string
     *
     * @ORM\Column(name="street1", type="string", length=255, nullable=false)
     */
    protected $street1;

    /**
     * @var string
     *
     * @ORM\Column(name="street2", type="string", length=255, nullable=true)
     */
    protected $street2;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var string
     * @ORM\Column(name="county", type="string", length=255, nullable=true)
     */
    protected $county;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=false)
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=255, nullable=false)
     */
    protected $postcode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_primary", type="boolean", nullable=false)
     */
    protected $primary = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shipping", type="boolean", nullable=false)
     */
    protected $shipping = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="billing", type="boolean", nullable=false)
     */
    protected $billing = false;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel", inversedBy="addresses")
     */
    protected $customer;

    /**
     * @var Carbon
     * @ORM\Column(name="added_at", type="datetime")
     */
    protected $addedAt;
    /**
     * @return boolean
     */
    public function isPrimary()
    {
        return $this->primary;
    }

    public function setAsPrimary($primary = true)
    {
        $this->primary = $primary;
    }

    /**
     * @return boolean
     */
    public function isDefaultShipping()
    {
        return $this->shipping;
    }

    public function setAsDefaultShipping($default = true)
    {
        $this->shipping = $default;
    }

    /**
     * @return boolean
     */
    public function isDefaultBilling()
    {
        return $this->billing;
    }

    public function setAsDefaultBilling($default = true)
    {
        $this->billing = $default;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    public function getNameNumber()
    {
        return $this->nameNumber;
    }

    public function getStreet1()
    {
        return $this->street1;
    }

    public function getStreet2()
    {
        return $this->street2;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getCounty()
    {
        return $this->county;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return PostalAddress
     */
    public function asPostalAddress()
    {
        return new PostalAddress(
            $this->getNameNumber(),
            $this->getStreet1(),
            $this->getStreet2(),
            $this->getCity(),
            $this->getCounty(),
            $this->getCountry(),
            $this->getPostcode()
        );
    }

}
