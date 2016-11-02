<?php
namespace Ntech\Customers\Tasks;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Ntech\CommandBus\CommandBus;
use Ntech\Invoices\Invoice;
use Ntech\Invoices\Models\SingleDoctrine\InvoiceDoctrineModel;
use Ntech\Uuid\Uuid;

class SendSummaryRemindersToAllCustomersHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var Uuid
     */
    private $companyId;

    public function __construct(
        EntityManager $entityManager,
        CommandBus $commandBus
    ) {
        $this->entityManager = $entityManager;
        $this->commandBus = $commandBus;
    }

    public function handle(SendSummaryRemindersToAllCustomersTask $task)
    {
        $this->companyId = $task->getCompanyId();
        /** Grab customers that qualify to be sent a summary email
         *  Right now the only qualification is "does a customer have due
         *  invoices/subscriptions"
         *  whether a subscription is due is also based on whether that
         *  subscription has due invoices. So to save processing we just
         *  run the one query to grab any due invoices and use those customer
         *  ids to send reminders.
         */
        $customers = $this->getCustomerIdsFromDueInvoices();

        if (count($customers) == 0) {
            return;
        }

        foreach ($customers as $customerId) {
            $this->commandBus->handle(
                new SendDueItemsSummaryReminderToCustomerTask(
                    Uuid::fromString($customerId)
                )
            );
        }
        return;
    }
    
    private function getCustomerIdsFromDueInvoices()
    {
        $qb = $this->entityManager->getRepository(InvoiceDoctrineModel::class)
            ->createQueryBuilder('i');
        $result = $qb->select('IDENTITY(i.customer) as customer_id')
            ->where('i.company = :companyId')
            ->andWhere('i.status = :invoiceStatus')
            ->andWhere('i.dueDate <= :today')
            ->groupBy('customer_id')
            ->setParameters([
                'companyId' => $this->companyId->toString(),
                'invoiceStatus' => Invoice::STATUS_UNPAID,
                'today' => Carbon::now()->toDateTimeString()
            ])
            ->getQuery()
            ->getArrayResult();
        return array_map(function ($row) {
            return $row['customer_id'];
        }, $result);
    }
}
