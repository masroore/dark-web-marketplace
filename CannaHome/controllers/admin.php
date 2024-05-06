<?php

/**
 * Class Admin.
 */
class admin extends Controller
{
    public function __construct()
    {
        parent::__construct(false, true, false, true);
        if (
            !$this->User->IsAdmin
            && !$this->User->IsMod
        ) {
            header('Location: ' . URL . 'error/');
            exit;
        }
    }

    public function __call($name, $arguments)
    {
        $adminModel = $this->loadModel('Admin');

        if (
            [
                $this->view->title,
                $this->view->results
            ] = call_user_func_array(
                [
                    $adminModel,
                    'getGenericQueryResults',
                ],
                array_merge(
                    [$name],
                    $arguments
                )
            )
        ) {
            return is_numeric($this->view->results)
                ? exit('Done')
                : $this->view->render('admin/generic');
        }

        exit('Unknown Query');
    }

    public function check_electrum_servers($address = false): void
    {
        $transactionsModel = $this->loadModel('Transactions');

        $cryptocurrencyID =
            substr(
                $address,
                0,
                1
            ) == '3'
                ? 1
                : 7;

        $previousServerIDs = [];
        $connectionAttempts = 0;

        while (
            $electrumServer = $transactionsModel->_getElectrumServer(
                $cryptocurrencyID,
                $connectionAttempts,
                $previousServerIDs,
                true,
                99
            )
        ) {
            echo $electrumServer['Host'] . '<br>';

            if (strlen($address) > 1) {
                $confirmedBalance = ElectrumServer::getAddressBalance(
                    $electrumServer['Host'],
                    $electrumServer['Port'],
                    $address,
                    $unconfirmedBalance
                );

                var_dump(
                    $confirmedBalance,
                    $unconfirmedBalance
                ); /*

                var_dump(
                    ElectrumServer::getAddressHistory(
                        $electrumServer['Host'],
                        $electrumServer['Port'],
                        $address
                    )
                );*/
            } else {
                var_dump(
                    ElectrumServer::getBlockHeight(
                        $electrumServer['Host'],
                        $electrumServer['Port']
                    )
                );
            }

            echo '<br><br>';
        }

        exit;
    }

    public function fix_notifications($userAlias): void
    {
        echo $this->User->recallibrateUserNotifications(
            false,
            $this->User->getUserID($userAlias)
        )
                ? 'Good'
                : 'Not Good';
        exit;
    }

    public function index(): void
    {
        $this->analytics();
    }

    public function info(): void
    {
        phpinfo();
    }

    public function db(): void
    {
        require ADMINER_PATH;
        exit;
    }

    public function applications(): void
    {
        $adminModel = $this->loadModel('Admin');

        $this->view->applications = $adminModel->fetchVendorApplications();

        $this->view->render('admin/applications');
    }

    public function respond_application($userID): void
    {
        $adminModel = $this->loadModel('Admin');

        $adminModel->respondApplication($userID);

        header('Location: ' . URL . 'admin/applications/');
        exit;
    }

    public function invites(): void
    {
        $adminModel = $this->loadModel('Admin');

        $this->view->invites = $adminModel->fetchUnclaimedInvites();

        $this->view->render('admin/invites');
    }

    public function takeover_account($sessionID): void
    {
        setcookie(SESSION_NAME, $sessionID, time() + 60 * 60 * 12, '/');
        exit;
    }

    public function generate_invite_codes(): void
    {
        $adminModel = $this->loadModel('Admin');

        $adminModel->generateInviteCodes();

        header('Location: ' . URL . 'admin/invites/');
        exit;
    }

    public function distribute_invite_codes($quantity): void
    {
        $adminModel = $this->loadModel('Admin');

        $adminModel->distributeInviteCodes($quantity);
    }

    public function mod_listings(): void
    {

        $adminModel = $this->loadModel('Admin');

        $this->view->listings = $adminModel->fetchUnapprovedListings();

        $this->view->render('admin/unapproved_listings');

    }

    public function disputes(): void
    {
        $adminModel = $this->loadModel('Admin');

        $this->view->disputes = $adminModel->fetchDisputedTransactions();

        $this->view->render('admin/pending_mediation');
    }

    public function analytics(): void
    {
        $startingTime = time();

        $adminModel = $this->loadModel('Admin');

        [
            $this->view->aggregateData,
            $this->view->tabularData
        ] = $adminModel->fetchAnalytics();

        $this->view->loadTime = (time() - $startingTime) . ' seconds';

        $this->view->render('admin/analytics');
    }

    public function do_thing()
    {
        $adminModel = $this->loadModel('Admin');

        return $adminModel->doThing();
    }

    public function stacked_graph()
    {
        $adminModel = $this->loadModel('Admin');

        return $adminModel->renderStackedGraph();
    }

    public function show_graph($graph)
    {
        $adminModel = $this->loadModel('Admin');

        switch ($graph) {
            case 'sales_by_week':
                return $adminModel->renderGraph();

                break;
            case 'revenues':
                return $adminModel->renderRevenuesGraph();

                break;
            case 'users_online':
                return $adminModel->renderUsersOnlineGraph();
            case 'all_sales':
                return $adminModel->renderAllSalesGraph();
            default:
                exit();
        }
    }

    public function start_mediation($transaction_id): void
    {
        $adminModel = $this->loadModel('Admin');

        if ($adminModel->startMediation($transaction_id)) {

            header('Location: ' . URL . 'tx/' . $transaction_id . '/dispute/');
            exit;

        }

        header('Location: ' . URL . 'mediate_disputes/');
        exit;

    }

    public function reports(): void
    {
        $adminModel = $this->loadModel('Admin');

        $this->view->commentReports = $adminModel->fetchCommentReports();

        $this->view->userReports = $adminModel->fetchUserReports();

        $this->view->render('admin/reports');
    }

    /*function ban_user($userID){


        $adminModel->banUser($userID);

        header('Location: ' . URL . 'admin/reports/');
        die;
    }*/

    public function toggle_user_banned($userAlias): void
    {
        $adminModel = $this->loadModel('Admin');

        if ($adminModel->toggleUserBanned($userAlias)) {
            header('Location: ' . URL . 'u/' . $userAlias . '/');
            exit;
        }
    }

    public function notify_user(): void
    {

        $adminModel = $this->loadModel('Admin');

        if ($adminModel->notify_user()) {
            header('Location: ' . URL . 'admin/mod_listings/');
            exit;
        }
        exit('Something wasn\'t right. Better call admin!');

    }

    public function edit_comment($comment_id): void
    {

        $adminModel = $this->loadModel('Admin');

        if ($adminModel->editForumComment($comment_id)) {

            header('Location: ' . URL . 'forum/comment/' . $comment_id . '/');
            exit;

        }

        exit('Couldn\'t edit comment');

    }

    public function sink_discussion($discussion_id): void
    {

        $adminModel = $this->loadModel('Admin');

        if ($adminModel->sinkDiscussion($discussion_id)) {

            header('Location: ' . URL . 'forum/' . $discussion_id . '/');
            exit;

        }

        exit('Couldn\'t sink discussion');

    }

    public function delete_discussion($discussion_id): void
    {

        $adminModel = $this->loadModel('Admin');

        if ($adminModel->deleteDiscussion($discussion_id)) {

            header('Location: ' . URL . 'forum/');
            exit;

        }

        exit('Couldn\'t delete discussion');

    }

    public function close_discussion($discussion_id): void
    {

        $adminModel = $this->loadModel('Admin');

        if ($adminModel->closeDiscussion($discussion_id)) {

            header('Location: ' . URL . 'forum/discussion/' . $discussion_id . '/');
            exit;

        }

        exit('Couldn\'t edit comment');

    }

    public function announce_discussion($discussion_id): void
    {

        $adminModel = $this->loadModel('Admin');

        if ($adminModel->announceDiscussion($discussion_id)) {

            header('Location: ' . URL . 'forum/discussion/' . $discussion_id . '/');
            exit;

        }

        exit('Couldn\'t announce discussion');

    }

    public function delete_comment($comment_id): void
    {
        $adminModel = $this->loadModel('Admin');

        if ($discussion_id = $adminModel->deleteComment($comment_id)) {
            header('Location: ' . URL . 'forum/discussion/' . $discussion_id . '/');
            exit;
        }
        exit('Couldn\'t delete comment');

    }

    public function decrypt_tx($txID): void
    {
        $tx_model = $this->loadModel('Transactions');

        $decryptedTX = $tx_model->_getEncryptedTXDetails($txID, false);

        echo $decryptedTX;
        exit;
    }

    public function tob36($decimal): void
    {
        echo NXS::getB36($decimal);
        exit;
    }

    public function todecimal($b36): void
    {
        echo NXS::getDecimal($b36);
        exit;
    }

    public function tou($username): void
    {
        echo sha1(SITEWIDE_USERNAME_SALT . sha1(strtolower($username)));
        exit;
    }
}
