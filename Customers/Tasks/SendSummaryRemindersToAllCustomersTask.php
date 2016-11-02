<?php
namespace Ntech\Customers\Tasks;

use Ntech\Queue\TaskShouldBeQueued;
use Ntech\Uuid\Uuid;
use NtechUtility\Tasks\Task;

class SendSummaryRemindersToAllCustomersTask implements Task, TaskShouldBeQueued
{
    /**
     * @var Uuid
     */
    private $companyId;

    public function __construct(
        Uuid $companyId
    ) {
        $this->companyId = $companyId;
    }

    /**
     * @return Uuid
     */
    public function getCompanyId()
    {
        return $this->companyId;
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
            Uuid::fromString($data['companyId'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'companyId' => $this->companyId->toString()
        ];
    }
}
