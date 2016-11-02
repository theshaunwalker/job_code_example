<?php
namespace Ntech\Subscriptions\Queries\Products;

use Doctrine\ORM\EntityManager;
use Ntech\CustomFields\CustomFieldsCollection;
use Ntech\CustomFields\Model\EntityAttribute\EntityAttributeModel;
use Ntech\Subscriptions\Products\SubscriptionProduct;
use NtechUtility\Cqrs\Query\QueryHandler;
use NtechUtility\Support\Collections\Collection;

class FindCustomFieldsForSubscriptionProductHandler implements QueryHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->eavQb = $entityManager->getRepository(EntityAttributeModel::class)->createQueryBuilder('eav');
    }

    public function handle(FindCustomFieldsForSubscriptionProductQuery $query)
    {
        $this->eavQb->where('eav.entityId = :subscriptionProductId')
            ->andWhere('eav.entityClass = :subscriptionProductClass');
        $this->eavQb->setParameters([
            'subscriptionProductId' => $query->getSubscriptionProductId()->toString(),
            'subscriptionProductClass' => SubscriptionProduct::class
        ]);

        $result = $this->eavQb->getQuery()->getResult();
        return CustomFieldsCollection::create(
            (new Collection($result))->map(function ($item) {
                return $item->getAttribute();
            })
        );
    }
}
