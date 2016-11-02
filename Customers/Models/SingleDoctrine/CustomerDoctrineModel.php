<?php
namespace Ntech\Customers\Models\SingleDoctrine;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ntech\Companies\Company;
use Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel;
use Ntech\Customers\Models\CustomerContactDoctrine\CustomerContactDoctrineModel;
use Ntech\Customers\Tags\Tag;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceCollection;
use Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel;
use Ntech\Support\ValueObjects\Traits\ArrayConstructor;
use Ntech\Uuid\Uuid;
use NtechUtility\Support\Collections\Collection;

/**
 * CustomerCustomers
 *
 * @ORM\Table(name="customers")
 * @ORM\Entity
 */
class CustomerDoctrineModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;
    /**
     * @var integer
     * @ORM\Column(name="count_id", type="integer")
    **/
    private $countId;
    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel")
     */
    private $company;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Ntech\Customers\Models\SingleDoctrine\CustomerAddressDoctrineModel", cascade={"persist", "remove"},
     *     mappedBy="customer", fetch="EAGER")
     */
    private $addresses;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="customer_since", type="datetime", nullable=false)
     */
    private $customerSince;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_on", type="datetime", nullable=false)
     */
    private $registeredOn;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Ntech\Customers\Models\CustomerContactDoctrine\CustomerContactDoctrineModel", mappedBy="customer")
     */
    private $contacts;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Ntech\Customers\Tags\Tag", inversedBy="customers")
     * @ORM\JoinTable(name="customer_tags_assigned",
     *      joinColumns={@ORM\JoinColumn(name="customer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     *
     */
    private $tags;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel", mappedBy="customer")
     */
    private $invoices;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Ntech\Payments\Models\PaymentSingleDoctrine\PaymentDoctrineModel", mappedBy="customer")
     */
    private $payments;
    /**
     * @var SubscriptionDoctrineModel
     * @ORM\OneToMany(targetEntity="Ntech\Subscriptions\Models\Subscription\SubscriptionDoctrineModel", mappedBy="customer")
     */
    private $subscriptions;
    /**
     * @var string
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Ntech\Customers\Models\CustomerUser\CustomerUserModel", mappedBy="customer")
    **/
    private $customerUsers;

    use ArrayConstructor;

    public function __construct(array $properties)
    {
        $this->constructObjectFromArray($properties);
        $this->generateUuid();
        $this->addresses = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return Uuid::fromString($this->id);
    }

    /**
     * @return CompanyDoctrineModel
     */
    public function getCompany()
    {
        return $this->company;
    }
    
    public function getCompanyId()
    {
        return $this->company->getId();
    }

    /**
     * @return CustomerAddressCollection
     */
    public function getAddresses()
    {
        return new CustomerAddressCollection($this->addresses->getValues());
    }

    public function getPrimaryAddress()
    {
        return $this->getAddresses()->getPrimary();
    }
    
    public function getBillingAddress()
    {
        return $this->getAddresses()->getBilling();
    }
    
    public function getShippingAddress()
    {
        return $this->getAddresses()->getShipping();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }

    /**
     * @return \Carbon
     */
    public function getCustomerSince()
    {
        return Carbon::instance($this->customerSince);
    }

    /**
     * @return \Carbon
     */
    public function getCreatedAt()
    {
        return Carbon::instance($this->createdAt);
    }

    /**
     * @return \Carbon
     */
    public function getUpdatedAt()
    {
        return Carbon::instance($this->updatedAt);
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function addTag(Tag $tag)
    {
        $this->getTags()->add($tag);
    }

    public function removeTag(Tag $tag)
    {
        $this->getTags()->removeElement($tag);
    }
    
    public function getContacts()
    {
        $items = [];
        foreach ($this->contacts as $key => $contact) {
            $items[$key] = $contact;
        }
        return new CustomerContactCollection($items);
    }

    /**
     * @return CustomerContactDoctrineModel|null
     */
    public function getPrimaryContact()
    {
        return $this->getContacts()->getPrimary();
    }

    public function getInvoices()
    {
        $this->invoices->initialize();
        return new InvoiceCollection($this->invoices->getValues());
    }

    public function getPayments()
    {
        $this->payments->initialize();
        return new Collection($this->payments->getValues());
    }

    /**
     * @return \DateTime
     */
    public function getRegisteredOn()
    {
        return $this->registeredOn;
    }

    /**
     * @return int
     */
    public function getCountId()
    {
        return $this->countId;
    }

    public function getUsers()
    {
        $users = [];
        foreach ($this->customerUsers as $customerUser) {
            $users[] = $customerUser->getUser();
        }
        return $users;
    }

    public function hasUser(Uuid $userId)
    {
        foreach ($this->customerUsers as $customerUser) {
            if ($userId == $customerUser->getUserId()) {
                return true;
            }
        }
        return false;
    }
}

