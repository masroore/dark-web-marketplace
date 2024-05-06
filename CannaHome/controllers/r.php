<?php

/**
 * Alias for login/index/[invite_code].
 */
class r
{
    public function __call($name, $arguments): void
    {
        require 'login.php';

        $login = new Login();
        call_user_func_array(
            [
                $login,
                'index',
            ],
            [$name]
        );

    }
}
