<?php
namespace Ntech\Subscriptions\Products\SignupFlow;

use NtechUtility\Serializer\Serializable;

class SignupSettings implements Serializable
{
    /**
     * URL to redirect to after a customer has successfully signed up
     * @var string
     */
    private $successRedirect;

    public function __construct(
        string $successRedirect = null
    ) {
        $this->successRedirect = $successRedirect;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            $data['redirects']['success']
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'redirects' => [
                'success' => $this->successRedirect
            ]
        ];
    }

    /**
     * @return string
     */
    public function getSuccessRedirect()
    {
        return $this->successRedirect;
    }

    /**
     *
     */
    public function redirectsOnSuccess()
    {
        return $this->successRedirect != null;
    }
}
