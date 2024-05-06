<?php

/**
 * Class TX
 * Alias for transactions/transaction/.
 */
class tx
{
    public function __call($name, $arguments): void
    {
        require 'transactions.php';

        $browse = new Transactions();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$browse, 'transaction'], $args);
    }
}
