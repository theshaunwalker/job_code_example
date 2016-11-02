<?php
namespace Ntech\Customers\Models\SingleDoctrine;

use Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel;
use Ntech\Database\Doctrine\DoctrineReadRepository;
use Ntech\Uuid\Uuid;

class CustomerDoctrineRepository extends DoctrineReadRepository
{

    /**
     * Class name of read model entity
     * @return string
     */
    public function getReadModelEntityClass()
    {
        return CustomerDoctrineModel::class;
    }

    public function forCompany(Uuid $companyId)
    {
        $this->where(['company' => $this->entityManager->getReference(CompanyDoctrineModel::class, $companyId->toString())]);
        return $this;
    }
}
