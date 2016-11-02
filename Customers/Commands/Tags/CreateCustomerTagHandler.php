<?php
namespace Ntech\Customers\Commands\Tags;

use Ntech\CommandBus\CommandHandler;
use Ntech\Customers\Events\CustomerTagCreated;
use Ntech\Customers\Tags\Tag;

class CreateCustomerTagHandler extends CommandHandler
{

    public function handle(CreateCustomerTagCommand $command)
    {
        $company = $command->getCompany();

        $tag = new Tag([
            'name' => $command->getTagName()
        ]);

        $company->addCustomerTag($tag);

        $command->getContext()->setCompany($company);
        // Fire events
        $command->newEvent(
            (new CustomerTagCreated($command->getContext(), $tag))
        );

        return $tag;
    }
}
