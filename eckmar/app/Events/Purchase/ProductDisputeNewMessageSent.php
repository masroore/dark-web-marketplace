<?php

namespace App\Events\Purchase;

use App\Purchase;
use App\User;
use Illuminate\Foundation\Events\Dispatchable;

class ProductDisputeNewMessageSent
{
    use Dispatchable;

    /**
     * User who purchased the product.
     *
     * @var mixed
     */
    public $buyer;

    /**
     * User who sells the product.
     *
     * @var mixed
     */
    public $vendor;

    /**
     * Product.
     */
    public $product;

    /**
     * Complete instance of a purchase.
     */
    public $purchase;

    /**
     * User that initiated dispute.
     *
     * @var User
     */
    public $initiator;

    /**
     * Create a new event instance.
     */
    public function __construct(Purchase $purchase, User $user)
    {

        $this->buyer = $purchase->buyer;
        $this->vendor = $purchase->vendor;
        $this->product = $purchase->offer->product;
        $this->purchase = $purchase;

        $this->initiator = $user;
    }
}
