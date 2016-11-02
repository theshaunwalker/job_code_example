<?php
namespace Ntech\Customers\Models\SingleDoctrine;

use NtechUtility\Support\Collections\Collection;

class CustomerContactCollection extends Collection
{

    public function getPrimary()
    {
        return $this->first(function ($key, $item) {
            return $item->isPrimary();
        });
    }

}
