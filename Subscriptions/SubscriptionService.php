<?php
namespace Ntech\Subscriptions;

use Ntech\Payments\PaymentsServiceContainer;

class SubscriptionService
{
    /**
     * @var PaymentsServiceContainer
     */
    private $paymentServiceContainer;

    public function __construct(
        PaymentsServiceContainer $paymentServiceContainer
    ) {
        $this->paymentServiceContainer = $paymentServiceContainer;
    }
    
    public function get()
    {
        
    }
}
