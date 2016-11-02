<?php
namespace Ntech\Customers\Commands\Tags;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Events\CustomerTagAdded;

class AddTagToCustomerHandler extends CommandHandler
{

    public function handle(AddTagToCustomerCommand $command)
    {
        $customer = $command->getCustomer();
        $tag = $command->getTag();

        $customer->addTag($tag);

        $command->getContext()->setCompany($customer->getCompany())->setCustomer($customer);

        $command->newEvent(
            (new CustomerTagAdded($command->getContext(), $tag))
        );

        return $tag;
    }
}
