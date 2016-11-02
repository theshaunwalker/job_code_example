<?php
namespace Ntech\Subscriptions\Commands\CustomFields;

use Ntech\CommandBus\Command;
use Ntech\Uuid\Uuid;

class RenameCustomFieldForSubscriptionProductCommand extends Command
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
     * @var string
     */
    private $newName;

    public function __construct(
        Uuid $subscriptionProductId,
        string $fieldSlug,
        string $newName
    ) {
        $this->subscriptionProductId = $subscriptionProductId;
        $this->fieldSlug = $fieldSlug;
        $this->newName = $newName;
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
     * @return string
     */
    public function getNewName()
    {
        return $this->newName;
    }
}
