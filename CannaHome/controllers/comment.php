<?php

/**
 * Class Comment
 * Alias for forum/comment/.
 */
class comment
{
    public function __call($name, $arguments): void
    {
        require 'forum.php';

        $browse = new Forum();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$browse, 'comment'], $args);
    }
}
