<?php

/**
 * Class Redirect.
 */
class redirect extends Controller
{
    public function __call($name, $arguments)
    {
        parent::__construct(false, true, false, true);

        $redirectDestination = false;
        if (isset($_SESSION['redirect_suffix'])) {
            $redirectDestination = URL . $_SESSION['redirect_suffix'];
            unset($_SESSION['redirect_suffix']);
        } else {
            switch ($name) {
                case 'account':
                    $redirectDestination = URL . 'account/';

                    break;
                default:
                    $redirectDestination =
                        URL .
                        (
                            $this->User->countUserNotifications()
                                ? 'account/'
                                : false
                        );
            }
        }

        if ($this->view->redirectDestination = $redirectDestination) {
            return $this->view->render('redirect/index');
        }
    }
}
