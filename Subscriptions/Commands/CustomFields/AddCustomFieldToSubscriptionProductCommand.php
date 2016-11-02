<?php
namespace Ntech\Subscriptions\Commands\CustomFields;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;
use NtechUtility\Eav\Fields\Field;

class AddCustomFieldToSubscriptionProductCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var Field
     */
    private $field;

    public function __construct(
        Uuid $subscriptionProductId,
        Field $field
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->field = $field;
    }

    /**
     * @return Uuid
     */
    public function getSubscriptionProductId()
    {
        return $this->subscriptionProductId;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }
}
