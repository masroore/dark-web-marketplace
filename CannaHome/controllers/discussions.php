<?php

/**
 * Class Discussions
 * Alias for forum/discussions/.
 */
class discussions
{
    public function __call($name, $arguments): void
    {
        require 'forum.php';

        $browse = new Forum();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$browse, 'discussions'], $args);
    }
}
