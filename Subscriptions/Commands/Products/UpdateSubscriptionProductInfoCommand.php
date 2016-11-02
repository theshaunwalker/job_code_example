<?php
namespace Ntech\Subscriptions\Commands\Products;

use Ntech\CommandBus\Command;
use Ntech\Subscriptions\Products\SubscriptionProductInfo;
use Ntech\Uuid\Uuid;

class UpdateSubscriptionProductInfoCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subProductId;
    /**
     * @var SubscriptionProductInfo
     */
    private $subProductInfo;

    public function __construct(
        Uuid $subProductId,
        SubscriptionProductInfo $subProductInfo
    ) {
        $this->subProductId = $subProductId;
        $this->subProductInfo = $subProductInfo;
    }

    /**
     * @return Uuid
     */
    public function getSubProductId()
    {
        return $this->subProductId;
    }

    /**
     * @return SubscriptionProductInfo
     */
    public function getSubProductInfo()
    {
        return $this->subProductInfo;
    }
}
