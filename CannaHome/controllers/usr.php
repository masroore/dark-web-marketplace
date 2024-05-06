<?php

/**
 * Class V
 * Alias for browse/user/.
 */
class usr
{
    public function __call($name, $arguments): void
    {

        require 'browse.php';

        $browse = new Browse();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$browse, 'user'], $args);

    }
}
