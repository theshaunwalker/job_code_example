<?php
namespace Ntech\Subscriptions\Products;

use NtechUtility\Serializer\Serializable;

class SubscriptionProductInfo implements Serializable
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $blurb;

    public function __construct(
        string $name,
        string $description,
        string $blurb
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->blurb = $blurb;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            $data['name'],
            $data['description'],
            $data['blurb']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'blurb' => $this->blurb
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getBlurb()
    {
        return $this->blurb;
    }
    
}
