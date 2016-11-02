<?php
namespace Ntech\Subscriptions\Queries\Products;

use Doctrine\ORM\EntityManager;
use Ntech\Subscriptions\Exceptions\SubscriptionProductDoesNotExist;
use Ntech\Subscriptions\Models\SubscriptionProduct\SubscriptionProductModel;
use NtechUtility\Cqrs\Query\QueryHandler;

class GetSubscriptionProductHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $subscriptionProductRepo;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->subscriptionProductRepo = $entityManager->getRepository(SubscriptionProductModel::class);
    }
    
    public function handle(GetSubscriptionProductQuery $query)
    {
        $product = $this->subscriptionProductRepo->find($query->getSubscriptionProductId()->toString());
        if ($product == null) {
            throw SubscriptionProductDoesNotExist::because("No Subscription Product for that ID");
        }
        return $product;
    }
}
