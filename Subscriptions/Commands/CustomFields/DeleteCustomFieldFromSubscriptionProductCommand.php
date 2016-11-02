<?php
namespace Ntech\Subscriptions\Commands\CustomFields;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class DeleteCustomFieldFromSubscriptionProductCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var string
     */
    private $fieldSlug;

    public function __construct(
        Uuid $subscriptionProductId,
        string $fieldSlug
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->fieldSlug = $fieldSlug;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return string
     */
    public function getFieldSlug()
    {
        return $this->fieldSlug;
    }
}
