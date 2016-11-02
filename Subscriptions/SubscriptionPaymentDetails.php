<?php
namespace Ntech\Subscriptions;

use Ntech\Uuid\Uuid;

class SubscriptionPaymentDetails
{

    /**
     * AUTOMATED type means we dont handle the charges, the
     * gateway handles all the processing we just listen to
     * webhooks.
     */
    const AUTOMATED = 1;
    /**
     * SAVED type means its saved payment details on our system
     * so when the subscription renews we use this payment method
     * to create a new charge ourselves.
     */
    const MANUAL = 2;

    /**
     * @var Uuid
     */
    private $subscriptionId;
    /**
     * @var string
     */
    private $methodKey;
    /**
     * ID of the subscription on the gateway
     * @var string
     */
    private $gatewayId;


}
