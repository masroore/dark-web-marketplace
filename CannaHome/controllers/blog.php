<?php

/**
 * Class Blog
 * Alias for forum/blog/.
 */
class blog
{
    public function __call($name, $arguments): void
    {
        require 'forum.php';

        $forum = new Forum();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$forum, 'blog'], $args);
    }
}
