<?php

namespace App\Policies;

use App\Product;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the product.
     *
     * @return mixed
     */
    public function view(?User $user, Product $product)
    {
        return $product->active;
    }

    /**
     * Determine whether the user can create products.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isVendor();
    }

    /**
     * Determine whether the user can update the product.
     *
     * @return mixed
     */
    public function update(User $user, Product $product)
    {
        if (null === $product) {
            return true;
        }

        // product can be updated by the owner or by the admin/moderator
        return ($product->user == $user || $user->isAdmin() || $user->hasPermission('products')) && $product->active;
    }

    /**
     * Determine whether the user can delete the product.
     *
     * @return mixed
     */
    public function delete(User $user, Product $product)
    {
        return false; // forbid deleting
    }

    /**
     * Determine whether the user can restore the product.
     *
     * @return mixed
     */
    public function restore(User $user, Product $product)
    {

    }

    /**
     * Determine whether the user can permanently delete the product.
     *
     * @return mixed
     */
    public function forceDelete(User $user, Product $product)
    {

    }
}
