<?php
namespace Ntech\Customers\Tags;

use Doctrine\ORM\Mapping as ORM;
use Ntech\Companies\Company;
use Ntech\Customers\Customer;
use Ntech\Database\Doctrine\HasSlug;
use Ntech\Support\ValueObjects\Traits\ArrayConstructor;

/**
 * CustomerTags
 *
 * @ORM\Table(name="customer_tags")
 * @ORM\Entity
 */
class Tag
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel", inversedBy="customerTags")
     */
    private $company;

    /**
     * @var Customer
     *
     * @ORM\ManyToMany(targetEntity="Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel", mappedBy="tags")
     */
    private $customers;

    use ArrayConstructor;
    use HasSlug;

    public function __construct(array $parameters)
    {
        $this->constructObjectFromArray($parameters);
        $this->generateSlug();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return mixed
     */
    public function getCustomers()
    {
        return $this->customers;
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

}

