<?php
namespace Ntech\Customers\Models\CustomerContactDoctrine;

use Ntech\Customers\Models\CustomerContactDoctrine\CustomerContactDoctrineModel;
use Ntech\Database\Doctrine\DoctrineReadRepository;

class CustomerContactDoctrineRepository extends DoctrineReadRepository
{

    /**
     * Class name of read model entity
     * @return string
     */
    public function getReadModelEntityClass()
    {
        return CustomerContactDoctrineModel::class;
    }

}
