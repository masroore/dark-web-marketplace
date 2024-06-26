<?php

/**
 * Login Controller
 * Controls the login processes.
 */
class login extends Controller
{
    /**
     * Construct this object by extending the basic Controller class.
     */
    public function __construct()
    {
        parent::__construct('narrow', false, false, true);
    }

    /**
     * Index, default action (shows the login form), when you do login/index.
     */
    public function index($inviteCode = false)
    {
        $this->redirectLoggedIn();

        if (isset($_SESSION['twoFA'])) {
            return $this->view->render('login/twofactor');
        }
        // Normal Log-in
        $this->view->inviteOnly = $this->db->invite_only;

        if ($inviteCode) {
            $inviteCode =
                $inviteCode !== 'index'
                    ? strip_tags($inviteCode)
                    : false;

            $loginModel = $this->loadModel('Login');

            $_SESSION['register_attempt'] = true;
            $_SESSION['login_invite_code'] = $inviteCode;
        }

        return $this->view->render('login/index');

    }

    /**
     * The login action, when you do login/login.
     */
    public function login(): void
    {
        $login_model = $this->loadModel('Login');

        if (!empty($_POST['password_confirm'])) {
            $_POST['action'] = 'Register';
        }

        switch (true) {
            case
            $_POST['action'] == 'Register'
            || (
                $_POST['user_action'] == 'register'
                && $_POST['action'] != 'Log In'
            )
            :
                if ($return = $login_model->registerNewUser()) {
                    header('Location: ' . (isset($_POST['prefix']) && preg_match('/[\w_]{3,20}/', $_POST['prefix']) ? 'http://' . $_POST['prefix'] . '.' . substr(URL, 7) : URL) . $return);
                    exit;
                }
                $location = 'Location: ' . URL . 'login/#register';
                header($location);
                exit;

                break;
                // case 'Log In'
            default:
                // check login status
                if ($destination = $login_model->login()) {
                    // if YES, then move user to dashboard/index
                    header('Location: ' . URL . $destination);
                    exit();
                } elseif (isset($_POST['submit_url']) && $return = $_POST['submit_url']) {
                    header('Location: ' . (isset($_POST['prefix']) && preg_match('/[\w_]{3,20}/', $_POST['prefix']) ? 'http://' . $_POST['prefix'] . '.' . substr(URL, 7) : URL) . $return);
                    exit;
                }
                $location = 'Location: ' . URL . 'login/';
                header($location);
                exit;

        }

    }

    public function login_pgp(): void
    {
        if (!isset($_SESSION['twoFA'])) {
            // SHOULDN'T BE HERE!!

            $location = 'Location: ' . URL . 'login/';
            header($location);
            exit;
        }

        $login_model = $this->loadModel('Login');

        $username = $_SESSION['credentials']['username'];
        $password = $_SESSION['credentials']['password'];

        // check login status
        if ($destination = $login_model->login($username, $password, false, false)) {
            // if YES, then move user to dashboard/index
            header('Location: ' . URL . $destination);
            exit();
        }
        $location = 'Location: ' . URL . 'login/';
        header($location);
        exit;

    }

    /**
     * The logout action, login/logout.
     */
    public function logout(): void
    {
        $login_model = $this->loadModel('Login');
        $login_model->logout();
        // redirect user to base URL
        header('Location: ' . URL);
        exit();
    }

    /**
     * Edit user name (show the view with the form).
     */
    public function editUsername(): void
    {
        // Auth::handleLogin() makes sure that only logged in users can use this action/method and see that page
        Auth::handleLogin();
        $this->view->render('login/editusername');
    }

    /**
     * Edit user name (perform the real action after form has been submitted).
     */
    public function editUsername_action(): void
    {
        $login_model = $this->loadModel('Login');
        $login_model->editUserName();
        $this->view->render('login/editusername');
    }

    /**
     * Set the new password.
     */
    public function setNewPassword(): void
    {
        $login_model = $this->loadModel('Login');
        // try the password reset (user identified via hidden form inputs ($user_name, $verification_code)), see
        // verifyPasswordReset() for more
        $login_model->setNewPassword();
        // regardless of result: go to index page (user will get success/error result via feedback message)
        header('Location: ' . URL . 'login/index');
        exit();
    }

    /**
     * Generate a captcha, write the characters into $_SESSION['captcha'] and returns a real image which will be used
     * like this: <img src="......./login/showCaptcha" />
     * IMPORTANT: As this action is called via <img ...> AFTER the real application has finished executing (!), the
     * SESSION["captcha"] has no content when the application is loaded. The SESSION["captcha"] gets filled at the
     * moment the end-user requests the <img .. >
     * If you don't know what this means: Don't worry, simply leave everything like it is.
     */
    public function showCaptcha(): void
    {
        $login_model = $this->loadModel('Login');
        $login_model->generateCaptcha();
    }
}
