<?php

/**
 * Class Post
 * Alias for forum/post/.
 */
class post
{
    public function __call($name, $arguments): void
    {
        require 'forum.php';

        $forum = new Forum();
        $args = array_merge([$name], $arguments);
        call_user_func_array([$forum, 'post'], $args);
    }
}
