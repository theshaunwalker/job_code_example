<?php
namespace Ntech\Customers\Queries;

use Doctrine\ORM\EntityManager;
use Ntech\Customers\Exceptions\CustomerDoesNotExist;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use NtechUtility\Cqrs\Query\QueryHandler;

class GetCustomerHandler implements QueryHandler
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $customerRepository;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->customerRepository = $entityManager->getRepository(CustomerDoctrineModel::class);
    }
    
    public function handle(GetCustomerQuery $query)
    {
        $customer = $this->customerRepository->find($query->getCustomerId()->toString());

        if ($customer == null) {
            throw CustomerDoesNotExist::because("No customer for ID [{$query->getCustomerId()->toString()}]");
        }

        return $customer;
    }
}
