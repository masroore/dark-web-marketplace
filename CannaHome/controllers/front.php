<?php

class front extends Controller
{
    /**
     * Construct this object by extending the basic Controller class.
     */
    public function __construct()
    {
        parent::__construct('main', true, true, true);
    }

    public function index()
    {
        if ($this->db->forum) {
            require 'forum.php';

            $forum = new Forum();

            return $forum->index();
        }
        /*require CONTROLLER_PATH . 'catalog.php';
        $catalog = new Catalog();
        $catalog->view->expandStores = true;

        return $catalog->listings(); */

        $catalog_model = $this->loadModel('Catalog');
        $forum_model = $this->loadModel('Forum');

        $this->view->frontpageListings = $catalog_model->fetchFrontpageListings();
        $this->view->latestUpdates = $forum_model->fetchLatestUpdates(LATEST_UPDATES_ON_MARKET_FRONTPAGE_COUNT, true);

        if (isset($_SESSION['newly_registered'])) {
            $this->view->localeOptions = $catalog_model->fetchLocaleOptions();

            if (!$this->User->IsVendor) {
                $this->User->sendMessage(NEW_MEMBER_WELCOME_MESSAGE);
            }
            unset($_SESSION['newly_registered']);
        } else {
            $this->view->localeOptions = false;
        }

        $this->view->render('front/index');

    }

    public function front(): void
    {
        $catalog_model = $this->loadModel('Catalog');
        $forum_model = $this->loadModel('Forum');

        $this->view->frontpageListings = $catalog_model->fetchFrontpageListings();
        $this->view->latestUpdates = $forum_model->fetchLatestUpdates(LATEST_UPDATES_ON_MARKET_FRONTPAGE_COUNT, true);

        if (isset($_SESSION['newly_registered'])) {
            $this->view->localeOptions = $catalog_model->fetchLocaleOptions();
            unset($_SESSION['newly_registered']);
        } else {
            $this->view->localeOptions = false;
        }

        $this->view->render('front/index');
    }
}
