<?php

/**
 * Class Discussion
 * Alias for forum/discussion/.
 */
class discussion
{
    public function __call($name, $arguments): void
    {
        require 'forum.php';

        $browse = new Forum();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$browse, 'discussion'], $args);
    }
}
