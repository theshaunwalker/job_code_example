<?php
namespace Ntech\Subscriptions\Commands\CustomFields;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;
use NtechUtility\Eav\Fields\FieldOptions;

class ModifyCustomFieldOptionsForSubscriptionProductCommand extends Command
{
    /**
     * @var Uuid
     */
    private $subscriptionProductId;
    /**
     * @var string
     */
    private $fieldSlug;
    /**
     * @var FieldOptions
     */
    private $newOptions;

    public function __construct(
        Uuid $subscriptionProductId,
        string $fieldSlug,
        FieldOptions $newOptions
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->fieldSlug = $fieldSlug;
        $this->newOptions = $newOptions;
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

    /**
     * @return FieldOptions
     */
    public function getNewOptions()
    {
        return $this->newOptions;
    }
}
