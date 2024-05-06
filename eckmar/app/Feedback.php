<?php

namespace App;

use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use Uuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    /**
     * Kinds of rates.
     *
     * @var array
     */
    public static $rates = ['quality_rate', 'communication_rate', 'shipping_rate'];

    /**
     * Feedback is posted on this product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    /**
     * Returns if this feedback has product.
     *
     * @return bool
     */
    public function hasProduct()
    {
        return $this->product_id != null;
    }

    public function buyer()
    {
        return $this->hasOne('App\User', 'id', 'buyer_id');
    }

    /**
     * Vendor of the feedback.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    /**
     * Set the Vendor of the feedback.
     */
    public function setVendor(Vendor $vendor): void
    {
        $this->vendor_id = $vendor->id;
    }

    /**
     * Sets the buyer of the purchase associated with the feedback.
     */
    public function setBuyer(User $buyer): void
    {
        $this->buyer_id = $buyer->id;
    }

    /**
     * Returns feedback type with first letter uppercase.
     *
     * @return string
     */
    public function getType()
    {
        return ucfirst($this->type);
    }

    /**
     * Set the product of the feedback.
     */
    public function setProduct(Product $product): void
    {
        $this->product_id = $product->id;
    }

    /**
     * Returns buyer name in format b***r (for buyer).
     */
    public function getHiddenBuyerName(): string
    {
        $buyer = $this->buyer->username;
        $firstChar = substr($buyer, 0, 1);
        $lastChar = substr($buyer, -1, 1);

        return $firstChar . '***' . $lastChar;
    }

    /**
     * Returns 'during last month' | 'during last three months' | 'during last six months' | 'during past year' | 'more than a year ago'.
     */
    public function getLeftTime(): string
    {
        $now = Carbon::now();
        $time = Carbon::parse($this->created_at);
        $timePassed = $now->diffInMonths($time);
        if ($timePassed < 1) {
            return 'During last month';
        } elseif ($timePassed >= 1 && $timePassed < 3) {
            return 'During last three months';
        } elseif ($timePassed >= 3 && $timePassed < 6) {
            return 'During last six months';
        } elseif ($timePassed >= 6 && $timePassed < 12) {
            return 'During last year';
        } elseif ($timePassed >= 12) {
            return 'More than a year ago';
        }
    }

    /**
     * Checks if feedback is low value.
     */
    public function isLowValue(): bool
    {
        return $this->product_value < (int) (config('marketplace.vendor_low_value_feedback'));
    }
}
