<?php
namespace Ntech\Customers\Queries;

use Doctrine\ORM\EntityManager;
use Ntech\Customers\Authorization\CustomerAccountSelectionView;
use Ntech\Customers\Models\CustomerUser\CustomerUserModel;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\QueryHandler;

class GetCustomerAccountSelectionViewForUserHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function handle(GetCustomerAccountSelectionViewForUserQuery $query)
    {
        $qb = $this->entityManager->getRepository(CustomerDoctrineModel::class)->createQueryBuilder('customer');
        $results = $qb->select('
            customer.id as customer_id, 
            customer.name as customer_name, 
            company.id as company_id, 
            company.name as company_name')
            ->innerJoin('customer.customerUsers', 'cuser')
            ->where('cuser.user = :userId')
            ->leftJoin('customer.company', 'company')
            ->setParameters([
                'userId' => $query->getUserId()->toString()
            ])
            ->getQuery()
            ->getArrayResult();

        $customerAccountSelection = new CustomerAccountSelectionView();
        foreach ($results as $result) {
            $customerAccountSelection->addSelection(
                Uuid::fromString($result['customer_id']),
                $result['customer_name'],
                $result['company_name']
            );
        }
        return $customerAccountSelection;
    }
}
