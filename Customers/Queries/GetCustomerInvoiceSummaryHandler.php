<?php
namespace Ntech\Customers\Queries;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Ntech\Customers\Models\Dashboard\InvoicesSummary\CustomerInvoicesSummary;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceCollection;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel;
use Ntech\Uuid\Uuid;
use NtechUtility\Cqrs\Query\QueryHandler;

class GetCustomerInvoiceSummaryHandler implements QueryHandler
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

    public function handle(GetCustomerInvoiceSummaryQuery $query)
    {
        $recentInvoices = $this->getRecentInvoices($query->getCustomerId(), $query->getTotalRecentCount());
        
        $windowInvoices = $this->getInvoicesInWindow($query->getCustomerId(), $query->getWindowDate());
        
        return new CustomerInvoicesSummary($recentInvoices, $windowInvoices, $query->getWindowDate());
    }

    public function getRecentInvoices(Uuid $customerId, int $totalRecentCount)
    {
        $qb = $this->entityManager->getRepository(InvoiceDoctrineModel::class)->createQueryBuilder('invoice');
        $recentInvoices = $qb->orderBy('invoice.date', 'DESC')
            ->where('invoice.customer = :customerId')
            ->setParameters([
                'customerId' => $customerId->toString()
            ])
            ->setMaxResults($totalRecentCount)
            ->getQuery()
            ->getResult();
        return new InvoiceCollection($recentInvoices);
    }

    public function getInvoicesInWindow(Uuid $customerId, Carbon $window)
    {
        $qb = $this->entityManager->getRepository(InvoiceDoctrineModel::class)->createQueryBuilder('invoice');
        $recentInvoices = $qb->orderBy('invoice.date', 'DESC')
            ->where('invoice.customer = :customerId')
            ->andWhere('invoice.date >= :windowDate')
            ->setParameters([
                'customerId' => $customerId->toString(),
                'windowDate' => $window->toDateTimeString()
            ])
            ->getQuery()
            ->getResult();
        return new InvoiceCollection($recentInvoices);
    }
}
