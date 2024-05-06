<?php

class catalog extends Controller
{
    /**
     * Construct this object by extending the basic Controller class.
     */
    public function __construct()
    {
        parent::__construct('main', true, true);
    }

    public function index($is_front_page = false): void
    {
        $this->listings(false, false, 1, $is_front_page);
    }

    public function listings($category = false, $sort = false, $page = 1, $query = false): void
    {
        // Preliminary Validation
        $page = (!is_numeric($page) || $page < 1) ? 1 : $page;
        // $category = (!is_numeric($category) || $category < 0) ? 0 : $category;

        if (
            $sort == false
            && isset($this->User->Attributes['Preferences']['CatalogFilter']['Listings_Sort'])
        ) {
            $sort = $this->User->Attributes['Preferences']['CatalogFilter']['Listings_Sort'];
        }

        $this->view->sortOptionsReminder = false;
        switch ($sort) {
            case 'popular':
            case 'price_asc':
            case 'price_desc':
            case 'price_m_asc':
            case 'price_m_desc':
            case 'price_v_asc':
            case 'price_v_desc':
            case 'name_asc':
            case 'name_desc':
            case 'fancy_rating':
            case 'id_asc':
            case 'id_desc':
                // $this->view->sortOptionsReminder = 'Remember, you can sort by <strong>rating</strong> to see the best listings';
                break;
            default:
                $sort = 'rating';
        }
        $this->User->updatePrefs(
            [
                'CatalogFilter' => [
                    'Listings_Sort' => $sort,
                ],
            ]
        );

        if ($query) {
            $this->view->query = htmlspecialchars($query);
            $this->view->isSearch = true;
        } else {
            $this->view->query = $this->view->isSearch = false;
        }

        $this->view->categoryAlias =
            (
                !$category
                || $category == 'index'
            )
                ? 'all'
                : htmlspecialchars($category);

        $category = $category == 'all' || $category == 'index' ? false : $category;

        $catalogModel = $this->loadModel('Catalog');

        if (isset($_SESSION['newly_registered'])) {
            $this->view->localeOptions = $catalogModel->fetchLocaleOptions();
            unset($_SESSION['newly_registered']);
        } else {
            $this->view->localeOptions = false;
        }

        $this->view->sortMode = $sort;

        $this->view->filterPreferences = $this->User->Attributes['Preferences']['CatalogFilter'];

        $this->view->shippingDestinations = $catalogModel->fetchShippingDestinations();
        $this->view->paymentMethods = $this->User->getListingPaymentMethods();

        if ($category && !is_numeric($category)) {
            $category = $catalogModel->getCategoryID($category);
        }

        [
            $this->view->listingCategories,
            $this->view->activeListingCategories,
            $visibleListingCategories,
            $categoryName
        ] = $catalogModel->fetchListingCategories(
            $category,
            false,
            $query
        );

        if ($categoryName) {
            $this->view->SiteName = $categoryName . ': ' . $this->view->SiteName;

            // Assumes higher category id comes after lower category id
            $isLast = true;
            foreach ($this->view->activeListingCategories as $activeListingCategory) {
                $this->view->breadcrumb[$activeListingCategory[1]] =
                    $isLast
                        ? false
                        : [
                            'URL' => '/listings/' . $activeListingCategory[2] . '/',
                        ];
                $isLast = false;
            }
            $this->view->breadcrumb['Listings'] = [
                'URL' => '/listings/',
            ];
            $this->view->breadcrumb = array_reverse($this->view->breadcrumb);
        }

        $this->view->categoryID = $category ? htmlspecialchars($category) : 0;

        [
            $this->view->listingCount,
            $this->view->trueListingCount,
            $this->view->listings
        ] = $catalogModel->fetchListings(
            $visibleListingCategories,
            $page,
            $sort,
            $query
        );

        $this->view->pageNumber = ceil($this->view->listingCount / LISTINGS_PER_PAGE) >= $page ? $page : 1;

        $this->view->render('catalog/listings');
    }

    public function apply_listings_filter(): void
    {
        $catalogModel = $this->loadModel('Catalog');

        $catalogModel->applyListingsFilter();

        header(
            'Location: ' .
            URL .
            preg_replace(
                '/[^\/\w]/',
                '',
                $_POST['return']
            )
        );
        exit;
    }

    public function faq($category = 'about', $faq = false): void
    {
        $category = $category == 'index' ? false : $category;

        $catalogModel = $this->loadModel('Catalog');

        if ($category == 'search') {

            $this->view->FAQs = $catalogModel->searchFAQs();

            $this->view->categoryID = $this->view->FAQID = false;

            [$this->view->categories, $this->view->FAQCount] = $catalogModel->fetchFAQCategories();

        } elseif ($category == 'vote') {

            if ($return_category = $catalogModel->voteFAQ($faq)) {
                header('Location: ' . URL . 'faq/' . $return_category . '/' . $faq . '/#' . $faq);
                exit;
            }
            exit('clonk');
            header('Location: ' . URL . 'faq/');
            exit;

        } else {

            if ($this->view->FAQs = $catalogModel->fetchFAQs($category)) {

                $this->view->categoryID = is_numeric($category) ? $category : $this->view->FAQs['faqs'][0]['category_id'];

                [$this->view->categories, $this->view->FAQCount] = $catalogModel->fetchFAQCategories();

                $this->view->FAQID = htmlspecialchars($faq);

                $this->view->SiteName = ($this->view->categoryID ? $this->view->categories[$this->view->categoryID]['name'] : 'FAQ') . ': ' . $this->view->SiteName;
            } else {
                header('Location: ' . URL . 'faq/');
                exit;
            }
        }

        $this->view->render('faq/index');
    }

    public function pages($page = false): void
    {

        $page_model = $this->loadModel('Pages');

        $catalogModel = $this->loadModel('Catalog');

        if ($page && $this->view->page = $catalogModel->fetchPage($page)) {

            $page = $page == 'index' ? 1 : $page;

            $this->view->pages = $catalogModel->fetchPageTitles();

            $this->view->current_page = htmlspecialchars($page);

            $this->view->render('pages/index');

        } else {
            header('Location: ' . URL . 'error/');
            exit;
        }

    }
}
