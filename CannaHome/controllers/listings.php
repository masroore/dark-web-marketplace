<?php

/**
 * Class Listings
 * Alias for catalog/listings/.
 */
class listings
{
    public function __call($name, $arguments): void
    {

        require 'catalog.php';

        $catalog = new Catalog();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$catalog, 'listings'], $args);

    }
}
