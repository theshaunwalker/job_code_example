<?php
namespace Ntech\Customers\Commands\Tags;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Events\CustomerTagRemoved;
use Ntech\Customers\Tags\AssignedTag;

class RemoveTagFromCustomerHandler extends CommandHandler
{

    public function handle(RemoveTagFromCustomerCommand $command)
    {
        $customer = $command->getCustomer();
        $tag = $command->getTag();

        $assignedTag = AssignedTag::whereCustomerId($customer->id)
            ->whereTagId($tag->id)
            ->first();

        if ($assignedTag != null) {
            $assignedTag->delete();
        }

        $command->getContext()->setCompany($customer->company)->setCustomer($customer);
        // Fire events
        $command->newEvent(
            (new CustomerTagRemoved($command->getContext(), $tag))
        );

    }
}
