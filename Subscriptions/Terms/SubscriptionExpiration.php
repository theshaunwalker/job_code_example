<?php
namespace Ntech\Subscriptions\Terms;

use Carbon\Carbon;
use NtechUtility\Serializer\Serializable;

class SubscriptionExpiration implements Serializable
{
    const ON_DATE = 1;
    const BY_INTERVAL_COUNT = 2;

    /**
     * One of self::$expirationTypes
     * @var string
     */
    private $type;
    /**
     * Date subscription expires
     * @var Carbon
     */
    private $date;
    /**
     * Amount of intervals before a subscription expires
     * @var int
     */
    private $intervalCount;

    private function __construct()
    {

    }

    public static function byDate(Carbon $date)
    {
        $expiration = new static();
        $expiration->type = self::ON_DATE;
        $expiration->date = $date;
        return $expiration;
    }

    public static function byIntervalCount(int $count)
    {
        $expiration = new static();
        $expiration->type = self::BY_INTERVAL_COUNT;
        $expiration->intervalCount = $count;
        return $expiration;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        switch ($data['type']) {
            case self::ON_DATE:
                return self::byDate(new Carbon($data['date']));
                break;
            case self::BY_INTERVAL_COUNT:
                return self::byIntervalCount($data['intervalCount']);
                break;
        }
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'type' => $this->getType(),
            'date' => $this->getDate(),
            'intervalCount' => $this->getIntervalCount()
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Carbon
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getIntervalCount()
    {
        return $this->intervalCount;
    }
}
