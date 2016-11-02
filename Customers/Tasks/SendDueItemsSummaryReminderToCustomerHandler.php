<?php
namespace Ntech\Customers\Tasks;

use Carbon\Carbon;
use Ntech\CommandBus\CommandBus;
use Ntech\Companies\CompanySettings;
use Ntech\Companies\Queries\Settings\GetCompanyEmailTemplateVariablesQuery;
use Ntech\Companies\Queries\Settings\GetCompanySettingsQuery;
use Ntech\Customers\Models\SingleDoctrine\CustomerDoctrineModel;
use Ntech\Customers\Queries\GetCustomerQuery;
use Ntech\Email\Commands\SendEmailCommand;
use Ntech\Email\Email;
use Ntech\EmailNotifications\Events\EmailNotificationSent;
use Ntech\EmailNotifications\Queries\HasEmailNotificationBeenSentQuery;
use Ntech\EmailTemplates\EmailTemplateCompiler;
use Ntech\EmailTemplates\EmailTemplateTagsCollection;
use Ntech\EmailTemplates\TemplateVariablesCollection;
use Ntech\Events\EventBus;
use Ntech\Invoices\Queries\FindDueInvoicesForCustomerQuery;
use Ntech\Notifications\Commands\NotifyCompanyCommand;
use Ntech\Notifications\Notification;
use Ntech\Notifications\Types\IdNotification;
use Ntech\Subscriptions\Queries\FindDueSubscriptionsForCustomerQuery;
use NtechUtility\Cqrs\Query\QueryProcessor;

class SendDueItemsSummaryReminderToCustomerHandler
{
    /**
     * @var QueryProcessor
     */
    private $queryProcessor;
    /**
     * @var EmailTemplateCompiler
     */
    private $emailTemplateCompiler;
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var EventBus
     */
    private $eventBus;

    public function __construct(
        QueryProcessor $queryProcessor,
        EmailTemplateCompiler $emailTemplateCompiler,
        CommandBus $commandBus,
        EventBus $eventBus
    ) {
        $this->queryProcessor = $queryProcessor;
        $this->emailTemplateCompiler = $emailTemplateCompiler;
        $this->commandBus = $commandBus;
        $this->eventBus = $eventBus;
    }

    public function handle(SendDueItemsSummaryReminderToCustomerTask $command)
    {
        /** @var CustomerDoctrineModel $customer */
        $customer = $this->queryProcessor->process(
            new GetCustomerQuery($command->getCustomerId())
        );

        // Check we havnt sent this notification already within an expected window
        $sentAlready = $this->queryProcessor->process(
            new HasEmailNotificationBeenSentQuery(
                $customer->getCompanyId(),
                $customer->getId(),
                'email_reminders.standard_summary_reminder',
                Carbon::now()->subHours(23)
            )
        );
        if ($sentAlready) {
            return;
        }

        // Check the customer has a primary contact to send to
        if ($customer->getPrimaryContact() == null) {
            $this->commandBus->handle(
                new NotifyCompanyCommand(
                    $customer->getCompanyId(),
                    'customer_email_missing',
                    Notification::LEVEL_WARNING,
                    new IdNotification(
                        CustomerDoctrineModel::class,
                        $customer->getId(),
                        $customer->getName(),
                        'user.customers.show'
                    ),
                    $customer->getId()
                )
            );
            return;
        }

        $subscriptions = $this->queryProcessor->process(
            (new FindDueSubscriptionsForCustomerQuery(
                $command->getCustomerId()
            ))->withPagination(1, 10)
        );
        $invoices = $this->queryProcessor->process(
            (new FindDueInvoicesForCustomerQuery(
                $command->getCustomerId()
            ))->withPagination(1, 10)
        );

        /** @var CompanySettings $companySettings */
        $companySettings = $this->queryProcessor->process(
            new GetCompanySettingsQuery($customer->getCompanyId())
        );

        /** @var TemplateVariablesCollection $templateVariables */
        $templateVariables = $this->queryProcessor->process(
            new GetCompanyEmailTemplateVariablesQuery($customer->getCompanyId())
        );
        $templateVariables->put(
            'subscriptions',
            $subscriptions
        )->put(
            'invoices',
            $invoices
        );

        $emailVariables = new EmailTemplateTagsCollection([
            'COMPANY_NAME' => $customer->getCompany()->getName(),
            'RECEIPT_NAME' => $customer->getPrimaryContact()->getFirstName() ?? $customer->getName()
        ]);

        $body = $this->emailTemplateCompiler->compile(
            'email_reminders.standard_summary_reminder',
            $templateVariables,
            $emailVariables
        );

        $email = Email::compose(
            $customer->getPrimaryContact()->getEmail(),
            $companySettings->get('email.reply_to'),
            'Payment Required - ' . $customer->getCompany()->getName(),
            $body,
            'Text version unavailable',
            [
                'fromName' => $customer->getCompany()->getName()
            ]
        );

        app('command.bus')->handle(new SendEmailCommand($email));

        $this->eventBus->fire(new EmailNotificationSent(
            $customer->getCompanyId(),
            $customer->getId(),
            'email_reminders.standard_summary_reminder',
            Carbon::now()
        ));
    }
}
