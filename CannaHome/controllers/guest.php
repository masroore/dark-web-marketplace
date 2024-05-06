<?php

class guest extends Controller
{
    /**
     * Construct this object by extending the basic Controller class.
     */
    public function __construct()
    {
        parent::__construct(false, false);

    }

    public function index(): void
    {

        $guest_model = $this->loadModel('Guest');

        $this->view->invalid = false;
        if (!empty($_POST) && isset($_POST['captcha'])) {
            if ($guest_model->handleCaptcha()) {
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
        $this->view->invalid = true;

        [$this->view->color, $this->view->customStylesheet] = $this->db->getSiteInfo('PrimaryColor', 'Stylesheet_CaptchaPage');

        if ($this->view->first = empty($_COOKIE['visitor'])) {
            $this->view->customStylesheet_First = $this->db->getSiteInfo('Stylesheet_CaptchaPage_First');
        }

        setcookie('visitor', 1, time() + 157680000);

        $this->view->render('guest/index');

    }
}
