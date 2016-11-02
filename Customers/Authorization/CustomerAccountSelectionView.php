<?php
namespace Ntech\Customers\Authorization;

use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Support\Collections\Collection;
use Ntech\Uuid\Uuid;

class CustomerAccountSelectionView extends Collection
{
    public function addSelection(Uuid $customerId, string $customerName, string $companyName)
    {
        $this->put($customerId->toString(), $customerName . ' (' . $companyName . ')');
    }
}
