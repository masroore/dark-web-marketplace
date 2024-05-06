<?php

/**
 * Class P
 * Alias for pages/.
 */
class p
{
    public function __call($name, $arguments): void
    {
        require 'pages.php';

        $pages = new Pages();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$pages, '__call'], $args);

    }
}
