<?php
namespace Ntech\Customers\Models;

use Doctrine\ORM\EntityManager;
use Ntech\Customers\Models\Customer\CustomerProjector;
use Ntech\Customers\Models\CustomerAddresses\CustomerAddressesProjector;
use Ntech\Customers\Models\CustomerContacts\CustomerContactsProjector;
use Ntech\Customers\Models\CustomerCreditBalance\CustomerCreditBalanceProjector;
use Ntech\Customers\Models\CustomerUser\CustomerUserProjector;
use NtechUtility\Cqrs\ReadModel\AbstractProjectorRegister;

class CustomersProjectorRegister extends AbstractProjectorRegister
{
    public function bootstrapProjectors($container)
    {
        $connection = $container->make('database.connection');
        $entityManager = $container->make(EntityManager::class);
        $this->addProjector(
            new CustomerProjector($entityManager)
        );
        $this->addProjector(
            new CustomerCreditBalanceProjector($connection, $entityManager)
        );
        $this->addProjector(
            new CustomerAddressesProjector($connection)
        );
        $this->addProjector(
            new CustomerContactsProjector($connection)
        );
        $this->addProjector(
            new CustomerUserProjector($entityManager)
        );
    }
}
