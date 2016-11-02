<?php
namespace Ntech\Customers\Models\SingleDoctrine;

use Ntech\Support\Collections\Collection;

class CustomerAddressCollection extends Collection
{

    public function getPrimary()
    {
        return $this->first(function ($key, $model) {
            if ($model->isPrimary()) {
                return $model;
            }
        });
    }
    public function getBilling()
    {
        $found = $this->first(function ($key, $model) {
            if ($model->isDefaultBilling()) {
                return $model;
            }
        });
        if (is_null($found)) {
            return $this->first();
        } else {
            return $found;
        }
    }

    public function getShipping()
    {
        $found = $this->first(function ($key, $model) {
            if ($model->isDefaultShipping()) {
                return $model;
            }
        });
        if (is_null($found)) {
            return $this->first();
        } else {
            return $found;
        }
    }
}
