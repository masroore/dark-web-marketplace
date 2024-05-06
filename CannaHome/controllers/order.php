<?php

/**
 * Class Order
 * Alias for transactions/start/.
 */
class order
{
    public function __call($name, $arguments): void
    {

        require 'transactions.php';

        $transactions = new Transactions();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$transactions, 'start'], $args);

    }
}
