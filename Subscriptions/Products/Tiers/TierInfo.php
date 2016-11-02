<?php
namespace Ntech\Subscriptions\Products\Tiers;

use NtechUtility\Serializer\Serializable;

class TierInfo implements Serializable
{
    /**
     * @var string
     */
    private $name;

    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            $data['name']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'name' => $this->name
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
