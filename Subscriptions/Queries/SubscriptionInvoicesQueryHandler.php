<?php
namespace Ntech\Subscriptions\Queries;

use Ntech\Invoices\Models\SingleDoctrine\DoctrineInvoiceRepository;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceCollection;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel;
use Ntech\Subscriptions\Subscription;
use NtechUtility\Cqrs\Query\QueryHandler;

class SubscriptionInvoicesQueryHandler implements QueryHandler
{
    /**
     * @var DoctrineInvoiceRepository
     */
    private $doctrineInvoiceRepository;

    public function __construct(
        DoctrineInvoiceRepository $doctrineInvoiceRepository
    ) {
        $this->doctrineInvoiceRepository = $doctrineInvoiceRepository;
    }

    public function handle(SubscriptionInvoicesQuery $domainQuery)
    {
        $queryBuilder = $this->doctrineInvoiceRepository->getDoctrineQueryBuilder();
        $queryBuilder->select('i')
            ->from(InvoiceDoctrineModel::class, 'i')
            ->orderBy('i.generatedOn', 'DESC')
            ->where('i.entityId = :entityId')
            ->andWhere('i.entityClass = :entityClass');
        $queryBuilder->setParameters([
            'entityId' => $domainQuery->getSubscriptionId()->toString(),
            'entityClass' => Subscription::class
        ]);
        return new InvoiceCollection($queryBuilder->getQuery()->getResult());
    }
}
