<?php

class go extends Controller
{
    public function __construct()
    {
        parent::__construct(false, true, false, true);
    }

    public function __call($name, $arguments): void
    {
        switch ($name) {
            case 'forum':
                $forumURL = $this->db->getSiteInfo('ForumURL');
                $location = $forumURL . '/' . ($arguments ? implode('/', $arguments) . '/' : false);
                header('Location: http://' . $location . '?xyz=' . session_id() . '-' . $_COOKIE['GUEST_ADMITTANCE_TOKEN']);

                break;
            case 'market':
                header('Location: http://' . $this->db->accessDomain . '/' . ($arguments ? implode('/', $arguments) . '/' : false));
        }

        exit;
    }
}
