<?php

class transactions extends Controller
{
    private $transactionsModel = false;

    /**
     * Construct this object by extending the basic Controller class.
     */
    public function __construct()
    {
        parent::__construct('main', true);
    }

    public function index(): void
    {
        header('Location: ' . URL . 'account/transactions/');
        exit;

        /*$this->transactions_model = $this->loadModel('Transactions');

        if( $this->transactions_model->countSellingTransactions() > 0){
            $this->sell();
        } else {
            $this->buy();
        }*/
    }

    public function buy(): void
    {
        $args = func_get_args();
        $args = array_merge(['buy'], $args);

        call_user_func_array([$this, 'overview'], $args);
    }

    public function sell(): void
    {
        $args = func_get_args();
        $args = array_merge(['sell'], $args);

        call_user_func_array([$this, 'overview'], $args);
    }

    public function order_with_payment_method($listingB36): void
    {
        if (isset($_POST['remember_choice'])) {
            $this->User->updateCryptocurrency($_POST['currency']);
        }

        header('Location: ' . URL . 'order/' . $listingB36 . '/' . $_POST['currency'] . '/');
        exit;
    }

    public function rate_all_transactions_positively(): void
    {
        $transactionModel = $this->loadModel('Transactions');

        $transactionModel->rateAllTransactionsPositively();

        header('Location: ' . URL . 'account/orders/ongoing/');
        exit;
    }

    public function update_transactions(): void
    {
        $transactionModel = $this->loadModel('Transactions');

        if (
            $this->iterativeRedirect(
                [
                    $transactionModel,
                    'updateTransaction',
                ],
                $_POST['transactions'],
                'transactions/update_transactions',
                'Processing Your Selections',
                [
                    'order',
                    'orders',
                ],
                $_POST
            )
        ) {
            $nextPage =
                !empty($_POST['current_page'])
                && is_numeric($_POST['current_page'])
                    ? $_POST['current_page']
                    : 1;

            header('Location: ' . URL . 'account/orders/ongoing/' . TRANSACTIONS_DEFAULT_SORTING_MODE . '/' . $nextPage . '/');
            exit;
        }
    }

    public function prepare_transactions(
        $option = false,
        $transactionModel = false,
        $return = 'account/orders/'
    ): void {
        $transactionModel = $transactionModel ?: $this->loadModel('Transactions');

        if (
            $option
            || isset($_POST)
        ) {
            unset($_SESSION['pending_transactions']);
            $_SESSION['pending_transactions']['signingPublicKey'] = true;
            $transactions = $transactionModel->getWithdrawableTransactions($option);
        }

        if (
            $this->iterativeRedirect(
                [
                    $transactionModel,
                    'prepareTransaction',
                ],
                $transactions,
                'transactions/prepare_transactions',
                'Preparing Your Withdrawal',
                [
                    'transaction',
                    'transactions',
                ],
                $_SESSION['pending_transactions']
            )
            && isset($_SESSION['pending_transactions']['transactions'])
        ) {
            header('Location: ' . URL . 'account/orders/#sign-transactions');
            exit;
        }

        unset($_SESSION['pending_transactions']);
        header('Location: ' . URL . $return);
        exit;
    }

    public function prepare_transactions_cryptocurrency($cryptocurrencyID)
    {
        $transactionModel = $this->loadModel('Transactions');

        if ($_POST['transaction_select'] = $transactionModel->fetchWithdrawableTransactionIDs($cryptocurrencyID)) {
            return $this->prepare_transactions(
                false,
                $transactionModel
            );
        }

        header('Location: ' . URL . 'account/transactions/');
        exit;
    }

    public function withdraw_transaction($txID)
    {
        return $this->prepare_transactions(
            $txID,
            false,
            'tx/' . $txID . '/fulfill/'
        );
    }

    public function sign_transactions($cryptocurrencyID): void
    {
        $transactionModel = $this->loadModel('Transactions');

        if ($transactionModel->signTransactions($cryptocurrencyID)) {
            unset($_SESSION['pending_transactions']);
            header('Location: ' . URL . 'account/transactions/');
            exit;
        }

        header('Location: ' . URL . 'account/orders/#sign-transactions');
        exit;
    }

    public function start($listingB36, $currencyISO = false): void
    {
        $listingID = NXS::getDecimal($listingB36);

        if (
            empty($listingID)
            || !is_numeric($listingID)
        ) {
            header('Location: ' . URL . 'transactions/');
            exit;
        }

        $transactionsModel = $this->loadModel('Transactions');

        $this->view->isVendor = $this->view->TXID = $this->view->isEditing = false;
        $this->view->listingID = $listingID;

        $preferredCryptocurrencyID =
            $currencyISO
                ? (
                    $this->User->getCryptocurrencyIDFromISO($currencyISO)
                        ?: $this->User->Attributes['Preferences']['CryptocurrencyID']
                )
                : $this->User->Attributes['Preferences']['CryptocurrencyID'];

        if (
            $listing = $this->view->listing = $transactionsModel->fetchListing(
                $listingID,
                true,
                $preferredCryptocurrencyID
            )
        ) {
            $this->view->shipping = $transactionsModel->fetchShippingInfo(
                $listingID,
                $preferredCryptocurrencyID
            );
            $this->view->vendorPGP = $this->User->Info(
                0,
                $listing['vendorID'],
                'PGP'
            );
            $this->view->paymentMethods = $transactionsModel->fetchListingPaymentMethods(
                $listingID,
                $preferredCryptocurrencyID
            );

            $this->view->option = 'order';
            $this->view->render('transactions/order');

        } else {
            header('Location: ' . URL . 'transactions/');
            exit;
        }

    }

    public function edit($transactionsModel, $transactionID, $listingID): void
    {
        if (empty($listingID) || !is_numeric($listingID)) {
            header('Location: ' . URL . 'transactions/');
            exit;
        }

        $this->view->isVendor = false;

        $this->view->TXID = $transactionsModel->getTransactionIdentifier($transactionID);

        $this->view->listingID = $listingID;

        if ($transaction = $this->view->transaction = $transactionsModel->fetchTransaction($transactionID)) {
            $this->view->shipping = $transactionsModel->fetchShippingInfo(
                $transaction['listing_id'],
                $transaction['paymentMethod']['cryptocurrency']->ID
            );
            $this->view->vendorPGP = $this->User->Info(
                0,
                $listing['vendorID'],
                'PGP'
            );

            $this->view->paymentMethods = $transactionsModel->fetchListingPaymentMethods(
                $transaction['listing_id'],
                $transaction['paymentMethod']['cryptocurrency']->ID
            );

            $this->view->option = 'order';

            $this->view->isEditing = true;

            $this->view->render('transactions/order');
        } else {
            header('Location: ' . URL . 'transactions/');
            exit;
        }

    }

    public function create_transaction($listingID): void
    {
        if (!$this->floodCheck('createTransaction', CREATE_ORDER_MINIMUM_WAIT)) {
            header('Location: ' . URL . 'order/' . NXS::getB36($listingID) . '/');
            exit;
        }

        $transactionsModel = $this->loadModel('Transactions');

        if ($transactionID = $transactionsModel->createTransaction($listingID)) {
            $transactionIdentifier = $transactionsModel->getTransactionIdentifier($transactionID);
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
            exit;
        }
        header('Location: ' . URL . 'order/' . NXS::getB36($listingID) . '/' . (isset($_POST['payment_method']) ? $_POST['payment_method'] . '/' : false));
        exit;

    }

    public function edit_transaction($transactionID): void
    {
        if (!$this->floodCheck('editTransaction', EDIT_ORDER_MINIMUM_WAIT)) {
            header('Location: ' . URL . 'tx/' . $transactionID . '/edit/');
            exit;
        }

        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if ($transactionsModel->editTransaction($transactionID)) {
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
            exit;
        }
        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/edit/');
        exit;

    }

    public function apply_promo($transactionID): void
    {
        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        $transactionsModel->applyPromoCodeTransaction($transactionID);

        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
        exit;
    }

    /*function cancel_order($transactionID){
        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if( $transactionsModel->deleteTransaction($transactionID) ){
            header('Location: ' . URL . 'account/transactions/');
            die;
        } else {
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
            die;
        }
    }*/

    public function qr_code(
        $depositAddress,
        $amount,
        $coin = 'bitcoin'
    ) {
        return QR::paymentRequest(
            $depositAddress,
            $amount,
            $coin
        );
    }

    public function transaction($transactionID, $option = false, $page = false)
    {
        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if (!$transactionID) {
            header('Location: ' . URL . 'account/transactions/');
            exit;
        }

        $hide_timeout =
            $option
            && $option !== 'dispute'
            && $option !== 'review'
            && $option !== 'pay'
            && $option !== 'finalize';

        if ($transaction = $transactionsModel->fetchTransaction($transactionID, $hide_timeout)) {
            $cryptocurrency = $this->view->cryptocurrency = $transaction['paymentMethod']['cryptocurrency'];

            /*$this->view->breadcrumb = [
                'Orders' => [
                    'URL' => '/account/orders/'
                ],
                $transactionIdentifier => false
            ];*/

            if ($transaction['status'] == 'rejected' || $transaction['status'] == 'refunded') {
                $option = 'review';
            }

            $this->view->listing = $listing = $transactionsModel->fetchListing($transaction['listing_id']);
            $this->view->TXID = $transactionIdentifier;
            $this->view->transaction = $transaction;
            $this->view->confirmed = !empty($transaction['redeem_script']);
            $this->view->paid = $this->view->confirmed && $transaction['status'] !== 'pending deposit';

            $this->view->priceBreakdown = $transaction['priceBreakdown'];
            if ($this->User->IsVendor) {
                $this->view->priceBreakdown = array_merge(
                    $this->view->priceBreakdown,
                    [
                        'network' => $cryptocurrency->formatValue($transaction['order']['Price']['transaction_fees'], true),
                    ]
                );
            }

            if (
                NXS::compareFloatNumbers(
                    $transaction['order']['Price']['marketplace_fee'],
                    $cryptocurrency->smallestIncrement,
                    '>='
                )
            ) {
                $this->view->priceBreakdown['marketplace'] = $cryptocurrency->formatValue($transaction['order']['Price']['marketplace_fee'], true);
            } else {
                $this->view->priceBreakdown['full'] = $cryptocurrency->appendName($transaction['order']['Price']['final_price']);
            }

            $this->view->isFree =
                isset($transaction['order']['Discount'])
                && $transaction['order']['Discount']
                && $transaction['order']['Discount'] == '100 %';

            $this->view->accepted = (
                $transaction['status'] == 'in transit'
                || $transaction['status'] == 'in dispute'
                || $transaction['status'] == 'pending feedback'
                || $transaction['status'] == 'expired'
            );

            $this->view->rejected = (
                $transaction['status'] == 'rejected'
                || $transaction['status'] == 'refunded'
            );

            $this->view->inDispute = $transaction['status'] == 'in dispute';

            $this->view->finalized = $transaction['status'] == 'pending feedback';

            $this->view->feedbackGiven = $transaction['feedback_given'];

            $this->view->inTransitTimeoutDays = $transaction['in_transit_timeout'];

            $this->view->isVendor = ($transaction['vendor_alias'] == $this->User->Alias);

            $this->view->cryptocurrencyFeeLevelOptions = false;
            if ($this->User->IsVendor) {
                $this->view->cryptocurrencyFeeLevelOptions = $transactionsModel->fetchCryptocurrencyFeeLevels($cryptocurrency->ID);
                $this->view->cryptocurrencyFeeLevel =
                    array_key_exists(
                        $this->User->Attributes['Preferences']['CryptocurrencyFeeLevel'],
                        $this->view->cryptocurrencyFeeLevelOptions
                    )
                        ? $this->User->Attributes['Preferences']['CryptocurrencyFeeLevel']
                        : CRYPTOCURRENCIES_CRYPTOCURRENCY_ID_DEFAULT;
            }

            if (!$option) {
                switch (true) {
                    case
                    $this->view->finalized
                    && (
                        !$this->view->isVendor
                        || (
                            !$this->view->feedbackGiven
                            && (
                                $transaction['escrow_enabled']
                                || $transaction['shipped']
                            )
                        )
                    )
                    :
                        $option = 'feedback';

                        break;
                    case $this->view->inDispute:
                        $option = 'dispute';

                        break;
                    case
                    $this->view->accepted
                    && (
                        !$this->view->finalized
                        || (
                            $this->view->isVendor
                            && !$transaction['escrow_enabled']
                            && !$transaction['shipped']
                        )
                    )
                    :
                        $option = $this->view->isVendor
                            ? 'fulfill'
                            : 'finalize';

                        break;
                    case $this->view->paid && !$this->view->isVendor:
                        $option = 'finalize';

                        break;
                    case $this->view->confirmed && !$this->view->isVendor:
                        $option = 'pay';

                        break;
                    default:
                        $option = 'review';
                }
            }

            $this->view->option = $option;

            $this->view->listingID = $transaction['listing_id'];

            $this->view->escrow = $transaction['escrow_enabled'];

            if ($this->view->isVendor && !$this->view->paid) {
                header('Location: ' . URL . 'account/transactions/');
                exit;
            }

            switch (true) {
                case $this->view->inDispute:
                    $time_left_description = 'Time left until a mediator is called in to assist in the dispute.';

                    break;
                case $this->view->finalized:
                    $time_left_description = 'Time left until the transaction is permanently deleted from the database.';

                    break;
                case $this->view->accepted:
                    $time_left_description = 'Time left until a dispute is automatically started.';

                    break;
                case $this->view->paid:
                    $time_left_description = 'Time left until the order is automatically cancelled and the funds will be returned to ' . ($this->view->isVendor ? 'the buyer\'s' : 'your') . ' specified return address';

                    break;
                default:
                    $time_left_description = 'Time left until the order is automatically cancelled.';

                    break;
            }

            $this->view->timeLeftDescription = $time_left_description;

            switch ($option) {
                case 'review':
                    if (!$this->view->confirmed) {
                        $this->view->vendor = $transactionsModel->fetchVendor(
                            $transaction['vendor_id']
                        );
                        $this->view->shipping = $transactionsModel->fetchShippingInfo(
                            $transaction['listing_id'],
                            $transaction['paymentMethod']['cryptocurrency']->ID
                        );
                        $this->view->paymentMethods = $transactionsModel->fetchListingPaymentMethods(
                            $transaction['listing_id'],
                            $transaction['paymentMethod']['cryptocurrency']->ID
                        );
                        $this->view->vendorPGP = $this->User->Info(
                            0,
                            $transaction['vendor_id'],
                            'PGP'
                        );

                        $this->view->publicKey = $this->User->getCryptocurrencyPublicKey($cryptocurrency->ID);
                        $this->view->returnAddress = false; // $this->User->getCryptocurrencyAddress($cryptocurrency->ID);
                    }

                    $this->view->render('transactions/review');

                    break;
                case 'edit':
                    if (!$this->view->isVendor) {
                        if (empty($_SESSION['order_post'])) {
                            $_SESSION['order_post']['quantity'] = $transaction['order']['Quantity'];
                            $_SESSION['order_post']['comments'] = $transaction['order']['Comments'];
                            $_SESSION['order_post']['shipping'] = $transaction['order']['ShippingID'];
                            $_SESSION['order_post']['address'] = $transaction['order']['Address'];
                            $_SESSION['order_post']['payment_method'] = $cryptocurrency->ISO;
                        }

                        $this->edit($transactionsModel, $transactionID, $transaction['listing_id']);
                    } else {
                        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
                        exit;
                    }

                    break;
                case 'pay':
                    if (
                        $transaction['timedOut']
                        && !$transaction['canExtendPaymentWindow']
                    ) {
                        header('Location: ' . URL . 'account/transactions/');
                        exit;
                    }

                    if (
                        $this->view->confirmed
                        && !$this->view->isVendor
                        && !$this->view->paid
                        && (
                            !$transaction['timedOut']
                            || $transaction['canExtendPaymentWindow']
                        )
                    ) {
                        if ($transaction['hasPaid']) {
                            $confirmedBalance = $transactionsModel->getAddressBalance(
                                $cryptocurrency->ID,
                                $transaction['order']['DepositAddress'],
                                REQUIRED_TX_CONFIRMATIONS_ORDER
                            );

                            $confirmed = NXS::compareFloatNumbers(
                                $confirmedBalance,
                                $transaction['value'],
                                '>='
                            );

                            if ($confirmed) {
                                $transactionsModel->_setTransactionsPlaced([$transactionID]);

                                header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/finalize/');
                                exit;
                            }

                            $this->view->feeBumpRequirement = false;
                            /*if(
                                !$transaction['hadFeeBump'] &&
                                $this->view->feeBumpRequirement = $transactionsModel->getTransactionFeeBumpRequirement(
                                    $transactionID,
                                    $transaction['order']['DepositAddress'],
                                    $transaction['redeem_script'],
                                    ($transaction['escrow_enabled'] ? 2 : 1)
                                )
                            ){
                                $unconfirmedBalance = $transactionsModel->getAddressBalance(
                                    $transaction['order']['DepositAddress'],
                                    0
                                );
                                if(
                                    NXS::compareFloatNumbers(
                                        $unconfirmedBalance,
                                        $transaction['value'] + $this->view->feeBumpRequirement,
                                        '>='
                                    ) &&
                                    $this->view->transaction['hadFeeBump'] = $transactionsModel->bumpTransactionsBitcoinFee([$transactionID])
                                )
                                    $this->view->feeBumpRequirement = 0;
                            }*/

                            return $this->view->render('transactions/finalize');
                        }
                        $unconfirmedBalance = $transactionsModel->getAddressBalance(
                            $cryptocurrency->ID,
                            $transaction['order']['DepositAddress'],
                            0
                        );

                        $this->view->unconfirmedBalance = $cryptocurrency->appendISO($cryptocurrency->parseValue($unconfirmedBalance, true));

                        if (
                            !$transaction['timedOut']
                            && $hasPaid = NXS::compareFloatNumbers(
                                $unconfirmedBalance,
                                $transaction['value'],
                                '>='
                            )
                        ) {
                            $this->view->transaction['hasPaid'] = $transactionsModel->markTransactionPaid($transactionID);
                        } else {
                            $minimumMarketOutput = $transactionsModel->_calculateMinimumMarketOutput(
                                $cryptocurrency,
                                CRYPTOCURRENCIES_FEE_LEVEL_DEFAULT,
                                BITCOIN_TRANSACTION_AVERAGE_SIZE_KB
                            );

                            if (
                                $this->view->insufficientPayment =
                                    NXS::compareFloatNumbers(
                                        $unconfirmedBalance,
                                        0,
                                        '>'
                                    )
                                    && NXS::compareFloatNumbers(
                                        $unconfirmedBalance,
                                        $transaction['order']['Price']['final_price'],
                                        '<'
                                    )
                            ) {
                                $this->view->insufficientPaymentDifference = $cryptocurrency->formatValue(max(
                                    $cryptocurrency->parseValue($transaction['order']['Price']['final_price'] - $unconfirmedBalance),
                                    $minimumMarketOutput
                                ));
                            }

                            if (
                                $hasDeposited =
                                    $transaction['hasDeposited'] == false
                                    && $unconfirmedBalance != 0
                                    && NXS::compareFloatNumbers(
                                        $unconfirmedBalance,
                                        $minimumMarketOutput,
                                        '>='
                                    )
                            ) {
                                $transactionsModel->markTransactionDeposited($transactionID)
                                && $transactionsModel->incrementBuyerNotification($transactionID);
                            }

                            $this->view->transaction['hasDeposited'] = $hasDeposited || $transaction['hasDeposited'];

                            if (!$transaction['timedOut']) {
                                $this->view->refreshSeconds = $transaction['secondsRemaining'] + REFRESH_PAYMENT_PAGE_SECONDS_AFTER_WINDOW_EXPIRY;
                            }
                        }

                        if (
                            $hasPaid
                            || $hasDeposited
                        ) {
                            $transactionsModel->incrementBuyerNotification($transactionID);
                        }

                        return $this->view->render($hasPaid ? 'transactions/finalize' : 'transactions/pay');

                    }
                    header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
                    exit;

                    break;
                case 'finalize':
                    if ($this->view->paid && !$this->view->isVendor) {
                        $this->view->render('transactions/finalize');
                    } else {
                        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
                        exit;
                    }

                    break;
                case 'fulfill':
                    if (
                        $this->view->accepted
                        && $this->view->isVendor
                        && (
                            !$this->view->finalized
                            || !$this->view->escrow
                        )
                    ) {
                        $nextOrderID = $transactionsModel->findNextOrderID($transactionID);
                        $nextOrderIdentifier = $transactionsModel->getTransactionIdentifier($nextOrderID);
                        $this->view->nextOrderHREF =
                            $nextOrderID
                                ? URL . 'tx/' . $nextOrderIdentifier . '/fulfill/'
                                : false;

                        $this->view->render('transactions/fulfill');
                    } else {
                        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
                        exit;
                    }

                    break;
                case 'feedback':
                    if (
                        $this->view->finalized
                        && (
                            !$this->view->isVendor
                            || !$this->view->feedbackGiven
                        )
                    ) {
                        if (!$this->view->isVendor) {
                            $rating = $transactionsModel->getRatings($transactionID);
                            $this->view->transactionRating = $rating['transactionRating'];
                            $this->view->transactionComments = $rating['comments'];
                            $this->view->ratingAttributes = $transactionsModel->getRatingAttributes();
                            $this->view->attributeID = $rating['AttributeID'];
                            $this->view->subscribeVendorToggleState =
                                $transactionsModel->getFeedbackSubscribeToggleState(
                                    $this->User->ID,
                                    $transaction['vendor_id']
                                );
                        }

                        $this->view->render('transactions/feedback');
                    } else {
                        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
                        exit;
                    }

                    break;
                case 'dispute':
                    if ($transaction['status'] == 'in dispute') {
                        if ($this->view->isMediator = $transaction['mediator_id'] == $this->User->ID) {
                            $this->view->vendor = $transactionsModel->fetchVendor(
                                $transaction['vendor_id'],
                                $transactionID
                            );
                        }

                        [
                            $this->view->pageNumber,
                            $this->view->disputeMessageCount,
                            $this->view->disputeMessages
                        ] = $transactionsModel->getDisputeMessages($transactionID, $page);

                        $this->view->render('transactions/dispute');
                    } else {
                        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
                        exit;
                    }

                    break;
            }
        } else {
            header('Location: ' . URL . 'account/transactions/');
            exit;
        }
    }

    public function renew_order_payment_window($transactionID): void
    {
        $this->checkCSRFToken();

        $transactionsModel = $this->loadModel('Transactions');

        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);
        $transactionsModel->renewOrderPaymentWindow($transactionID);

        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/pay/#pay');
        exit;
    }

    public function claim_order_deposit_refund($transactionID)
    {
        $this->checkCSRFToken();

        $transactionsModel = $this->loadModel('Transactions');

        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if ($transactionsModel->claimRefundLateTransactionDeposit($transactionID)) {
            return $this->prepare_transactions(
                $transactionID,
                $transactionsModel
            );
        }

        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/pay/#pay');
        exit;
    }

    public function confirm_transaction($transactionID): void
    {
        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if ($transactionsModel->confirmTransaction($transactionID)) {
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/pay/#pay');
        } else {
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/review/');
        }

        exit;
    }

    /*function pay_transaction($transactionID){

        $transactionsModel = $this->loadModel('Transactions');

        if( $transactionsModel->payTransaction($transactionID) ){

            header("Location: " . URL . "tx/" . $transactionID . "/finalize/");
            die;

        } else {

            header('Location: ' . URL . 'tx/' . $transactionID . '/pay/');
            die;

        }

    }
    */

    /*function recover_funds($transactionID){

        $transactionsModel = $this->loadModel('Transactions');

        $suffix = $transactionsModel->recoverDepositFunds($transactionID) ? '#recover-funds' : false;

        header('Location: ' . URL . 'tx/' . $transactionID . '/pay/' . $suffix);
        die;

    }
    */

    public function respond_transaction($transactionID): void
    {
        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if ($next_step = $transactionsModel->respondTransaction($transactionID, $isEscrow)) {

            switch ($next_step) {
                case 'feedback':
                case 'fulfill':
                    header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/' . $next_step . '/');
                    exit;

                    break;
                case 'rejected':
                    $location = $isEscrow
                        ? 'account/transactions/'
                        : 'transactions/prepare_transactions/' . $transactionIdentifier . '/';

                    header('Location: ' . URL . $location);
                    exit;

                    break;
                default:
                    header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/review/#' . $next_step);
                    exit;
            }

        } else {
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
            exit;
        }

        /*} elseif( $response = $transactionsModel->respondTransaction($transactionID) ){

            switch( $response ){

                case 'Accept Order':

                    header('Location: ' . URL . 'tx/' . $transactionID . '/fulfill/');
                    die;

                break;
                case 'Reject Order':

                    header('Location: ' . URL . 'account/transactions/');
                    die;

                break;
                case 'feedback':

                    header('Location: ' . URL . 'tx/' . $transactionID . '/feedback/');
                    die;

                break;

            }

        } else {

            header('Location: ' . URL . 'tx/' . $transactionID . '/review/');
            die;

        }*/

    }

    public function refund_transaction($transactionID): void
    {
        $this->checkCSRFToken();

        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if ($transactionsModel->refundTransaction($transactionID)) {
            if ($this->User->IsMod) {
                header('Location: ' . URL . 'admin/disputes/');
            } else {
                header('Location: ' . URL . 'account/transactions/');
            }
            exit;
        }

        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
        exit;
    }

    public function finalize_transaction($transactionID): void
    {
        $this->checkCSRFToken();

        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if ($transactionsModel->finalizeTransaction($transactionID)) {
            if ($this->User->IsMod) {
                header('Location: ' . URL . 'admin/disputes/');
            } else {
                header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/feedback/');
            }
            exit;
        }

        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/');
        exit;
    }

    public function rate_transaction($transactionID): void
    {
        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if ($transactionsModel->rateTransaction($transactionID)) {
            header('Location: ' . URL . 'account/transactions/' . (!$this->User->IsVendor ? 'finalized/' : false));
            exit;
        }
        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/feedback/');
        exit;

    }

    public function send_message($transactionID): void
    {
        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        if (
            $_POST['proposal_type'] == 'refund'
            && (
                (
                    $this->User->IsVendor
                    && $_POST['percentage'] == 100
                )
                || (
                    !$this->User->IsVendor
                    && $_POST['percentage'] == 0
                )
            )
        ) {
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/dispute/#release-funds');
            exit;
        }

        $message = $transactionsModel->sendMessage($transactionID);

        if (
            $message['id']
            && $message['type'] == 'refund'
            && !$this->User->IsMod
        ) {
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/dispute/#accept-proposal-' . $message['id']);
            exit;
        }
        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/dispute/');
        exit;

    }

    public function call_mediator($transactionID): void
    {
        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        $transactionsModel->callMediator($transactionID);

        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/dispute/');
        exit;

    }

    public function withdraw_proposal($proposal_id): void
    {
        $transactionsModel = $this->loadModel('Transactions');

        if ($transactionID = $transactionsModel->withdrawProposal($proposal_id)) {
            $transactionIdentifier = $transactionsModel->getTransactionIdentifier($transactionID);

            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/dispute/');
            exit;
        }
        header('Location: ' . URL . 'account/transactions/');
        exit;

    }

    public function accept_proposal($proposal_id): void
    {
        $this->checkCSRFToken();

        $transactionsModel = $this->loadModel('Transactions');

        if ([$success, $transactionID] = $transactionsModel->acceptProposal($proposal_id)) {
            if ($success) {
                header('Location: ' . URL . $success);
                exit;
            } elseif ($transactionIdentifier = $transactionsModel->getTransactionIdentifier($transactionID)) {
                header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/dispute/');
                exit;
            }
        }

        header('Location: ' . URL . 'account/transactions/');
        exit;
    }

    public function sign_proposal($proposal_id): void
    {
        $transactionsModel = $this->loadModel('Transactions');

        if ([$success, $transactionID, $destination] = $transactionsModel->signProposal($proposal_id)) {
            $transactionIdentifier = $transactionsModel->getTransactionIdentifier($transactionID);
            if ($success) {
                header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/' . $destination . '/');
                exit;
            }
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/dispute/#accept-proposal-' . $proposal_id);
            exit;

        }
        header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/dispute/');
        exit;

    }

    public function toggle_shipped($transactionID, $source = 'overview'): void
    {
        $transactionsModel = $this->loadModel('Transactions');
        $transactionID = $transactionsModel->getTransactionID($transactionID, $transactionIdentifier);

        $transactionsModel->toggleTransactionShipped($transactionID);

        switch ($source) {
            case 'overview':
                header('Location: ' . URL . 'account/orders/');
                exit;

                break;
                // case 'details':
            default:
                header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/fulfill/');
                exit;

                break;
        }
    }
}
