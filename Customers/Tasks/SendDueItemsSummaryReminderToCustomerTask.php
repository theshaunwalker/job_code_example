<?php
namespace Ntech\Customers\Tasks;

use Ntech\Queue\TaskShouldBeQueued;
use Ntech\Uuid\Uuid;
use NtechUtility\Tasks\Task;

class SendDueItemsSummaryReminderToCustomerTask implements Task, TaskShouldBeQueued
{
    /**
     * @var Uuid
     */
    private $customerId;

    public function __construct(
        Uuid $customerId
    ) {
        $this->customerId = $customerId;
    }

    /**
     * @return Uuid
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * 'default' for main queue
     * @return string
     */
    public function getQueueName(): string
    {
        return 'default';
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            Uuid::fromString($data['customerId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'customerId' => $this->customerId->toString()
        ];
    }
}
