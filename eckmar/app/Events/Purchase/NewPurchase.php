<?php

namespace App\Events\Purchase;

use App\Purchase;
use Illuminate\Foundation\Events\Dispatchable;

class NewPurchase
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
     * Create a new event instance.
     */
    public function __construct(Purchase $purchase)
    {
        $this->buyer = $purchase->buyer;
        $this->vendor = $purchase->vendor;
        $this->product = $purchase->offer->product;
        $this->purchase = $purchase;
    }
}
