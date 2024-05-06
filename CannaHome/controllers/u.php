<?php

/**
 * Class U
 * Alias for browse/user/.
 */
class u
{
    public function __call($name, $arguments): void
    {

        require 'browse.php';

        $browse = new Browse();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$browse, 'user'], $args);

    }
}
