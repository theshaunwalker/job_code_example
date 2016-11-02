<?php
namespace Ntech\Customers\Commands\Tags;

use Carbon\Carbon;
use Ntech\CommandBus\Command;
use Ntech\Companies\Company;

class CreateCustomerTagCommand extends Command
{

    protected $company;
    protected $tagName;
    protected $createdAt;

    public function __construct(Company $company, $tagName, $createdAt = null)
    {
        $this->company = $company;
        $this->tagName = $tagName;
        $this->createdAt = ($createdAt == null) ? $createdAt : Carbon::now();
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return mixed
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @return null|static
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
