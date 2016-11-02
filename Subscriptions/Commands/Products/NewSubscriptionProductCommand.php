<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\Command;
use Ntech\Subscriptions\Products\SubscriptionProductInfo;
use Ntech\Uuid\Uuid;

class NewSubscriptionProductCommand extends Command
{
    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var Uuid
     */
    private $companyId;
    /**
     * @var SubscriptionProductInfo
     */
    private $info;

    public function __construct(
        Uuid $id,
        Uuid $companyId,
        SubscriptionProductInfo $info
    ) {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->info = $info;
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
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return SubscriptionProductInfo
     */
    public function getInfo()
    {
        return $this->info;
    }
}
