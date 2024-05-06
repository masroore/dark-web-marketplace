<?php

/**
 * Class FAQ
 * Alias for catalog/faq/.
 */
class faq
{
    public function __call($name, $arguments): void
    {

        require 'catalog.php';

        $catalog = new Catalog();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$catalog, 'faq'], $args);

    }
}
