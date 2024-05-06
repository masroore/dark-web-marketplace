<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = ['product_id', 'user_id'];

    /**
     * Returns if the whishlist existring with product and user.
     *
     * @return bool
     */
    public static function added(Product $product, User $user)
    {
        return self::where('product_id', $product->id)->where('user_id', $user->id)->exists();
    }

    /**
     * Returns the collectionWishlist if the logged user is favorited product.
     *
     * @return Collection
     */
    public static function getWish(Product $product)
    {
        if (auth()->check()) {
            return self::where('product_id', $product->id)->where('user_id', auth()->user()->id);
        }

        return null;
    }

    /**
     * Return \App\User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Returns \App\Product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
