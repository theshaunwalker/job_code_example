<?php
namespace Ntech\Customers\Queries;

use Doctrine\ORM\EntityManager;
use Ntech\Companies\Models\SingleDoctrine\CompanyDoctrineModel;
use Ntech\Customers\Exceptions\CustomerDoesNotExist;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use NtechUtility\Cqrs\Query\QueryHandler;

class GetCustomerByCompanyAndEmailHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $customerRepo;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->customerRepo = $entityManager->getRepository(CustomerDoctrineModel::class);
    }
    
    public function handle(GetCustomerByCompanyAndEmailQuery $query)
    {
        $customer = $this->customerRepo->findOneBy([
            'email' => $query->getEmail(),
            'company' => $this->entityManager->getReference(CompanyDoctrineModel::class, $query->getCompanyId()->toString())
        ]);
        if ($customer == null) {
            throw CustomerDoesNotExist::because("No Customer for Company [{$query->getCompanyId()->toString()}] with email [{$query->getEmail()}]");
        }
        return $customer;
    }
}
