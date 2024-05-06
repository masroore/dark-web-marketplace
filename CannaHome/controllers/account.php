<?php

class account extends Controller
{
    public function __construct()
    {
        parent::__construct('main', true, false, true);

        if ($this->User->IsVendor) {
            $this->view->vendorListingCount = $this->User->Info('ActiveListingCount');
        } else { // if($this->User->IsTester){
            $this->view->hasInvites = $this->User->getInvitesEntitlement($this->view->inviteCount);
        }

        // $this->view->collapsedNav = $this->User->Attributes['Preferences']['CollapsedNav'];
    }

    public function index(): void
    {
        $this->overview();
    }

    public function export_statistics($queryIdentifier): void
    {
        $accountModel = $this->loadModel('Account');
        if (
            [
                $title,
                $results
            ] = $accountModel->fetchUserQueryResult(
                $queryIdentifier,
                true
            )
        ) {
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename=' . $queryIdentifier . '.csv');

            echo implode(
                PHP_EOL,
                array_merge(
                    [
                        implode(
                            ',',
                            array_keys($results[0])
                        ),
                    ],
                    array_map(
                        function ($result) {
                            return implode(
                                ',',
                                strip_tags($result)
                            );
                        },
                        $results
                    )
                )
            );
            exit;
        }
    }

    public function statistics($queryIdentifier = ACCOUNT_STATISTICS_DEFAULT_QUERY_IDENTIFIER)
    {
        NXS::showError();

        if (isset($_POST['query'])) {
            header('Location: ' . URL . 'account/statistics/' . htmlspecialchars($_POST['query']) . '/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        if (
            (
                [
                    $this->view->title,
                    $this->view->results
                ] = $accountModel->fetchUserQueryResult($queryIdentifier)
            )
            || [
                $this->view->title,
                $this->view->results
            ] = $accountModel->fetchUserQueryResult(ACCOUNT_STATISTICS_DEFAULT_QUERY_IDENTIFIER)
        ) {
            $this->view->queryIdentifier = $queryIdentifier;
            $this->view->userQueries = $accountModel->getUserQueries();

            return $this->view->render('account/statistics');
        }

        header('Location: ' . URL . 'account/');
        exit;
    }

    public function overview($mode = false): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        $accountModel = $this->loadModel('Account');
        $forumModel = $this->loadModel('Forum');

        if ($this->view->showStats = $mode == 'stats') {
            $this->view->userStats = $accountModel->getUserStats();
            $this->view->distinctions = $accountModel->getUserDistinctions();
        }

        if ($mode == 'stats') {
            $this->User->recallibrateUserNotifications(false);
        }

        if ($this->User->IsVendor) {
            $this->User->allocateUserDomains();
        }

        $accountModel->getDashboardNotifications();
        [
            $forumEntryCount,
            $this->view->forumEntries
        ] = $forumModel->fetchForumEntries(
            false,
            false,
            1,
            (
                $this->view->forumEntryCount > ACCOUNT_HOMEPAGE_MAX_FORUM_ENTRIES
                    ? $this->view->forumEntryCount
                    : ACCOUNT_HOMEPAGE_MAX_FORUM_ENTRIES
            )
        );

        /*if($this->User->IsVendor){
            $this->view->javascripts = ['/public/js/notifications.js'];
            $this->view->enableLiveUpdates = $this->User->Attributes['Preferences']['LiveUpdate'];
        }*/

        $this->view->render('account/overview');
    }

    public function download_backup(): void
    {
        $accountModel = $this->loadModel('Account');
        $accountModel->generateUserBackup();
    }

    public function delete_account()
    {
        return true;

        $accountModel = $this->loadModel('Account');

        if ($accountModel->deleteAccount()) {
            Session::destroy();
            header('Location: ' . URL . 'login/');
            exit();
        }
        header('Location: ' . URL . 'account/');
        exit();

    }

    public function settings(): void
    {
        $accountModel = $this->loadModel('Account');

        $this->view->preferences = $accountModel->getUserPreferences();

        $this->view->canUploadPicture =
            $this->User->IsVendor
            || $this->User->IsAdmin
            || $this->User->IsMod
            || $isStarBuyer = $this->User->ascertainUserClass(
                USER_CLASS_ID_STAR_BUYERS,
                1
            );

        $this->view->render('account/settings');
    }

    public function update_settings(): void
    {
        $this->checkCSRFToken();

        $accountModel = $this->loadModel('Account');
        $isStarBuyer = false;

        // HANDLE UPLOADs

        // Profile Picture
        if (
            (
                $this->User->IsVendor
                || $this->User->IsAdmin
                || $this->User->IsMod
                || $isStarBuyer = $this->User->ascertainUserClass(
                    USER_CLASS_ID_STAR_BUYERS,
                    1
                )
            )
            && !empty($_FILES['file']['name'])
        ) {
            if ($isStarBuyer) {
                $m = new Memcached();
                $m->addServer('localhost', 11211);
                $mKey = 'recentAction-' . $this->User->ID . '-uploadedPicture';
            }

            foreach ($_FILES as $key => $value) {
                if (empty($value['name'])) {
                    continue;
                }

                if (
                    $isStarBuyer
                    && $hadUploaded = $m->get($mKey)
                ) {
                    $_SESSION['temp_notifications']['invalidUpload'] = [
                        'Content' => 'You cannot change your profile picture more than once per day',
                        'Anchor' => false,
                        'Group' => 'Settings',
                        'Dismiss' => '.',
                        'Design' => [
                            'Color' => 'red',
                            'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                        ],
                    ];
                } else {
                    switch ($key) {
                        case 'file': // PROFILE PICTURE
                            $file = $accountModel->uploadFile(
                                $key,
                                true,
                                true,
                                ($isStarBuyer ? USER_CLASS_PRIVILEGES_AVATAR_WIDTH_STAR_BUYERS : false),
                                ($isStarBuyer ? USER_CLASS_PRIVILEGES_AVATAR_HEIGHT_STAR_BUYERS : false),
                                array_merge(
                                    [
                                        [
                                            'width' => ($isStarBuyer ? USER_CLASS_PRIVILEGES_AVATAR_WIDTH_STAR_BUYERS : AVATAR_IMAGE_WIDTH),
                                            'height' => ($isStarBuyer ? USER_CLASS_PRIVILEGES_AVATAR_HEIGHT_STAR_BUYERS : AVATAR_IMAGE_HEIGHT),
                                            'suffix' => IMAGE_MEDIUM_SUFFIX,
                                        ],
                                        [
                                            'width' => AVATAR_IMAGE_THUMBNAIL_WIDTH,
                                            'height' => AVATAR_IMAGE_THUMBNAIL_HEIGHT,
                                            'suffix' => IMAGE_THUMBNAIL_SUFFIX,
                                        ],
                                    ],
                                    $isStarBuyer
                                        ? []
                                        : [
                                            [
                                                'width' => AVATAR_IMAGE_SMALL_WIDTH,
                                                'height' => AVATAR_IMAGE_SMALL_HEIGHT,
                                                'suffix' => IMAGE_SMALL_SUFFIX,
                                            ],
                                        ]
                                )
                            );

                            break;
                    }

                    $imageURL = $file['filepath'] . $file['filename'];

                    $validUpload =
                        false !== $file
                        && empty($file['error'])
                        && $imageURL !== 'SS';

                    if ($validUpload) {
                        $_POST['uploads'][$key] = $file['imageID'];

                        if ($isStarBuyer) {
                            $m->set($mKey, true, USER_CLASS_PRIVILEGES_UPLOADS_INTERVAL);
                        }
                    } else {
                        $_SESSION['temp_notifications']['invalidUpload'] = [
                            'Content' => 'One or more images could not be uploaded. Please try again',
                            'Anchor' => false,
                            'Group' => 'Settings',
                            'Dismiss' => '.',
                            'Design' => [
                                'Color' => 'red',
                                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                            ],
                        ];
                    }
                }
            }
        }

        $return = $accountModel->updateSettings();

        header('Location: ' . URL . 'account/settings/' . ($return !== true ? '#' . $return : false));
        exit;
    }

    public function submit_vendor_application(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        $accountModel = $this->loadModel('Account');

        $accountModel->submitVendorApplication();

        header('Location: ' . URL . 'pages/vendors/');
        exit;
    }

    public function support_overview($filterMode = SUPPORT_OVERVIEW_DEFAULT_FILTER_MODE, $sortMode = SUPPORT_OVERVIEW_DEFAULT_SORT_MODE, $pageNumber = 1): void
    {
        if (!$this->User->IsMod) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $this->view->canSkipCaptcha =
            $this->User->IsVendor
            || $this->User->Attributes['TotalTransacted'] >= AMOUNT_TRANSACTED_SKIP_CAPTCHA;

        $this->view->isSupportPage = true;
        $this->view->pageNumber = $pageNumber;

        [
            $this->view->conversationCount,
            $this->view->conversations
        ] = $accountModel->fetchConversations(1);

        $chatStatuses = $accountModel->fetchChatStatuses($this->view->recursiveChatStatuses);

        $this->view->filterModeOptions = [
            'all' => 'All tickets',
            'assigned' => 'Assigned',
            'waiting' => 'Waiting',
        ];

        foreach ($chatStatuses as $chatStatus) {
            $this->view->filterModeOptions[strtolower($chatStatus['Title'])] = $chatStatus['Title'];
        }

        $this->view->filterMode = $filterMode;
        $this->view->sortMode = $sortMode;

        $supportChatCount = $this->view->supportChatCount = $accountModel->countChats($filterMode);

        if ($supportChatCount > 0) {
            $offset = NXS::getOffset(
                $supportChatCount,
                SUPPORT_OVERVIEW_CHATS_PER_PAGE,
                $pageNumber
            );

            $supportChats = $this->view->supportChats = $accountModel->fetchChats(
                $filterMode,
                $sortMode,
                SUPPORT_OVERVIEW_CHATS_PER_PAGE,
                $offset
            );

            $this->view->numberOfPages = ceil($supportChatCount / SUPPORT_OVERVIEW_CHATS_PER_PAGE);

            $this->view->modUsernames = false;
            if (
                $this->User->Alias == 'Finn'
                || $this->User->Alias == 'TestAdmin'
            ) {
                $this->view->modUsernames = $accountModel->fetchModUsernames();
            }

            foreach ($supportChats as $supportChat) {
                $this->view->inlineStylesheet .= '
					#assigned-' . $supportChat['ID'] . ":checked ~ table [data-ticket-id='" . $supportChat['ID'] . "'] {
					display: table-row;
					}
					#assigned-" . $supportChat['ID'] . ":checked ~ table [data-ticket-id='" . $supportChat['ID'] . "'] .visible-assigned {
						display: block;
					}
					#assigned-" . $supportChat['ID'] . ":checked ~ table [data-ticket-id='" . $supportChat['ID'] . "'] .checkbox > i {
						border-color: #52987E;
					}
					#assigned-" . $supportChat['ID'] . ":checked ~ table [data-ticket-id='" . $supportChat['ID'] . "'] .checkbox > i::after {
						opacity: 1;
					}
				";
            }
        } else {
            $this->view->supportChats = false;
        }

        $this->view->render('account/support_overview');
    }

    public function support_chat($targetUserAlias = false, $pageNumber = 1)
    {
        $accountModel = $this->loadModel('Account');

        $pageNumber = $pageNumber ?: 1;

        $this->view->isSupportPage = true;
        $this->view->pageNumber = 1;

        $this->view->canSkipCaptcha =
            $this->User->IsVendor
            || $this->User->Attributes['TotalTransacted'] >= AMOUNT_TRANSACTED_SKIP_CAPTCHA;

        [
            $this->view->conversationCount,
            $this->view->conversations
        ] = $accountModel->fetchConversations(1);

        $targetUserAlias = $this->view->targetUserAlias = $targetUserAlias ? htmlspecialchars($targetUserAlias) : $this->User->Alias;

        if (
            !$this->User->IsMod
            && strtolower($targetUserAlias) !== strtolower($this->User->Alias)
        ) {
            header('Location: ' . URL . 'account/support/');
            exit;
        }

        $this->view->chatStatuses = $accountModel->fetchChatStatuses();

        $this->view->modUsernames = false;
        if (
            $this->User->Alias == 'Finn'
            || $this->User->Alias == 'TestAdmin'
        ) {
            $this->view->modUsernames = $accountModel->fetchModUsernames();
        }

        $chatID = $this->view->chatID = $accountModel->getChatID($targetUserAlias);
        if ($chatID) {
            $chat = $this->view->chat = $accountModel->fetchChat(
                $chatID,
                CHAT_MESSAGES_SORT_MODE_DEFAULT,
                $pageNumber
            );

            if ($this->User->IsMod) {
                $this->view->relevantTransactions = $accountModel->fetchUserTransactions($targetUserAlias);

                $this->view->activeTransaction =
                    $chat['ActiveTransactionID']
                        ? $accountModel->getTransactionDetails($chat['ActiveTransactionID'])
                        : false;
            }

            $this->view->supportPageNumber = htmlspecialchars($pageNumber);
            $this->view->numberOfPages = ceil($chat['messageCount'] / CHAT_MESSAGES_ENTRIES_PER_PAGE_DEFAULT);
        } else {
            $this->view->chat = false;
            $this->view->supportPageNumber = $this->view->numberOfPages = 1;
        }

        return $this->view->render('account/support');
    }

    public function support($arg1 = false, $arg2 = false, $arg3 = false)
    {
        if (
            strlen($arg1) < 3
            && is_numeric($arg1)
            && $arg1 > 0
        ) {
            if ($this->User->IsMod) {
                return $this->support();
            }

            $pageNumber = $arg1;

            return $this->support_chat(false, $pageNumber);
        }

        $targetUserAlias = $arg1;
        $pageNumber = $arg2;

        if ($this->User->IsMod && $targetUserAlias == false) {
            return $this->support_overview();
        }

        return $this->support_chat($targetUserAlias, $pageNumber);
    }

    public function create_new_ticket()
    {
        if (!$this->User->IsMod) {
            header('Location: ' . URL . 'account/support/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        if ($chatID = $accountModel->getChatID($_POST['subject_alias'])) {
            return $this->send_chat_message(
                $chatID,
                $accountModel
            );
        }

        $chatID = $accountModel->createChat(
            CHAT_STATUS_ID_INITIAL_MOD,
            CHAT_ROLE_SUPPORT,
            false,
            false,
            false,
            CHAT_ROLE_COLOR_SUPPORT
        );
        if ($chatID) {
            $userAlias = $accountModel->getChatSubjectAlias($chatID);

            header('Location: ' . URL . 'account/support/' . $userAlias . '/');
            exit;
        }
    }

    public function toggle_chat_subscription(
        $chatID,
        $chatSubscriptionRole = false
    ): void {
        if (!$this->User->IsMod) {
            header('Location: ' . URL . 'account/support/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        if (
            $userAlias = $accountModel->getChatSubjectAlias($chatID)
        ) {
            $accountModel->toggleChatSubscription($chatID, $chatSubscriptionRole);

            header('Location: ' . URL . 'account/support/' . $userAlias . '/');
            exit;
        }

        header('Location: ' . URL . 'account/support/');
        exit;
    }

    public function set_chat_transaction_id($chatID): void
    {
        if ($this->User->IsMod) {
            $accountModel = $this->loadModel('Account');
            $transactionsModel = $this->loadModel('Transactions');

            $transactionID =
                !empty($_POST['transaction_id_specify'])
                    ? $_POST['transaction_id_specify']
                    : $_POST['transaction_id_select'];

            if ($userAlias = $accountModel->getChatSubjectAlias($chatID)) {
                if ($transactionID = $transactionsModel->getTransactionID($transactionID)) {
                    $accountModel->setChatSubscriptionTransactionID($chatID, $transactionID);
                }

                $_SESSION['justSetChatTransactionID'] = true;

                header('Location: ' . URL . 'account/support/' . $userAlias . '/');
                exit;
            }
        }

        header('Location: ' . URL . 'account/support/');
        exit;
    }

    public function change_chat_status(
        $chatID,
        $statusID
    ): void {
        if (!$this->User->IsMod) {
            header('Location: ' . URL . 'account/support/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        if ($userAlias = $accountModel->getChatSubjectAlias($chatID)) {
            $accountModel->changeChatStatus(
                $chatID,
                $statusID
            );

            header('Location: ' . URL . 'account/support/' . $userAlias . '/');
            exit;
        }

        header('Location: ' . URL . 'account/support/');
        exit;
    }

    public function send_chat_message(
        $chatID = false,
        $accountModel = false,
        $transactionsModel = false
    ): void {
        $accountModel = $accountModel ?: $this->loadModel('Account');
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        if (!$this->User->IsMod) {
            $chatID = $accountModel->getChatID($this->User->Alias);

            if ($chatID == false) {
                $chatID = $accountModel->createChat(
                    CHAT_STATUS_ID_INITIAL_USER,
                    CHAT_ROLE_SUBJECT,
                    $this->User->Alias
                );
                if ($chatID) {
                    $return =
                        URL . (
                            !empty($_POST['chat_return'])
                            && preg_match(
                                REGEX_URL_SAFE,
                                $_POST['chat_return']
                            )
                                ? $_POST['chat_return']
                                : 'account/support/' . ($this->User->IsMod ? $userAlias . '/' : false)
                        );

                    header('Location: ' . $return);
                    exit;
                }
                header('Location: ' . URL . 'account/support/');
                exit;

            }
        }

        switch (true) {
            case $this->User->IsMod:
                $color = CHAT_ROLE_COLOR_SUPPORT;

                break;
            default:
                $color = null;
        }

        if (
            $userAlias = $accountModel->getChatSubjectAlias($chatID)
        ) {
            $transactionID = $transactionsModel->getTransactionID($_POST['transaction_id']);
            $chatMessageID = $accountModel->createChatMessage(
                $chatID,
                $color,
                false,
                false,
                $transactionID,
                true,
                (
                    $this->User->IsMod
                        ? [
                            CHAT_STATUS_ID_OPEN,
                            CHAT_STATUS_ID_CLOSED,
                        ]
                        : [
                            CHAT_STATUS_ID_CLOSED,
                        ]
                ),
                (
                    $this->User->IsMod
                        ? CHAT_STATUS_ID_ONGOING
                        : CHAT_STATUS_ID_OPEN
                )
            );

            if ($this->User->IsMod) {
                $accountModel->updateChatNote(
                    $chatID
                );

                if ($chatMessageID) {
                    $accountModel->toggleChatSubscription(
                        $chatID,
                        CHAT_ROLE_SUPPORT,
                        $this->User->ID,
                        false,
                        $chatMessageID
                    );
                }
            }

            $return =
                URL . (
                    !empty($_POST['chat_return'])
                        ? $_POST['chat_return']
                        : 'account/support/' . ($this->User->IsMod ? $userAlias . '/' : false)
                );

            header('Location: ' . $return);
            exit;
        }

        header('Location: ' . URL . 'account/support/');
        exit;
    }

    public function update_chats(): void
    {
        if (!$this->User->IsMod) {
            header('Location: ' . URL . 'account/support/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $redirect = $accountModel->updateChats() ?: (URL . 'account/support/');

        header('Location: ' . $redirect);
        exit;
    }

    public function delete_chat_message($chatMessageID): void
    {
        if ($this->User->IsMod) {
            $accountModel = $this->loadModel('Account');

            if (
                (
                    $chatMessage = $accountModel->findChatMessage($chatMessageID)
                )
                && $accountModel->deleteChatMessage($chatMessageID)
            ) {
                header('Location: ' . URL . 'account/support/' . $chatMessage['SubjectUserAlias'] . '/');
                exit;
            }
        }

        header('Location: ' . URL . 'account/support/');
        exit;
    }

    public function edit_chat_message($chatMessageID): void
    {
        if ($this->User->IsMod) {
            $accountModel = $this->loadModel('Account');

            if (
                $accountModel->editChatMessageContent($chatMessageID)
                && $redirect = $_POST['redirect']
            ) {
                header('Location: ' . $redirect);
                exit;
            }
        }

        header('Location: ' . URL . 'account/support/');
        exit;
    }

    public function conversations($initialPage = null): void
    {
        $page = $initialPage ?: 1;

        $accountModel = $this->loadModel('Account');

        $this->view->isSupportPage = false;

        $this->view->canSkipCaptcha =
            $this->User->IsVendor
            || $this->User->Attributes['TotalTransacted'] >= AMOUNT_TRANSACTED_SKIP_CAPTCHA;

        $page = $this->view->pageNumber =
            is_numeric($page) && $page > 0
                ? $page
                : 1;
        $message_page = $this->view->messagePage = 1;

        $this->view->importantOnly = false;
        $this->view->conversationMode = 'all';

        [
            $conversation_count,
            $conversations
        ] = [
            $this->view->conversationCount,
            $this->view->conversations
        ] = $accountModel->fetchConversations($page);

        if (
            !$initialPage
            && $conversations[0]['earliestUnreadMessageID']
        ) {
            header('Location: ' . URL . 'account/message/' . $conversations[0]['earliestUnreadMessageID'] . '/');
            exit;
        }

        [
            $message_count,
            $alias,
            $userRole,
            $messages
        ] = [
            $this->view->messageCount,
            $this->view->recipientAlias,
            $this->view->userRole,
            $this->view->messages
        ] =
            $conversations
                ? $accountModel->fetchConversation(
                    $conversations[0]['userAlias'],
                    1
                )
                : false;

        $this->view->recipientPGP =
            $conversations
                ? $accountModel->getUserPGP($conversations[0]['userAlias'])
                : false;

        $this->view->trustedVendor =
            $this->User->IsVendor
            && $this->User->Attributes['TotalTransacted'] >= AMOUNT_TRANSACTED_TRUSTED_VENDOR;

        $this->view->render('account/conversations');
    }

    public function messages(): void
    {
        // ALIAS FOR conversations();
        call_user_func_array(
            [$this, 'conversations'],
            func_get_args()
        );
    }

    public function conversation(
        $alias,
        $initialMode = null,
        $page = 1
    ) {
        $mode = $initialMode ?: 'all';

        $accountModel = $this->loadModel('Account');

        switch ($mode) {
            case 'important':
                break;
            default:
                $mode = 'all';
        }
        $this->view->conversationMode = $mode;
        $importantOnly = $this->view->importantOnly = $mode == 'important';

        $this->view->canSkipCaptcha =
            $this->User->IsVendor
            || $this->User->Attributes['TotalTransacted'] >= AMOUNT_TRANSACTED_SKIP_CAPTCHA;

        if ($conversation_page = $this->view->pageNumber = $accountModel->findConversationPage($alias)) {
            $page = $this->view->messagePage = is_numeric($page) && $page > 0 ? $page : 1;

            [
                $conversation_count,
                $conversations
            ] = [
                $this->view->conversationCount,
                $this->view->conversations
            ] = $accountModel->fetchConversations($conversation_page);

            if (!$initialMode) {
                $lowercaseAlias = strtolower($alias);
                $thisConversation = array_filter(
                    $conversations,
                    fn ($conversation) => strtolower($conversation['userAlias']) == $lowercaseAlias
                );

                if ($thisConversation[0]['earliestUnreadMessageID']) {
                    header('Location: ' . URL . 'account/message/' . $thisConversation[0]['earliestUnreadMessageID'] . '/');
                    exit;
                }
            }

            [
                $message_count,
                $recipientAlias,
                $userRole,
                $messages,
                $hasImportant
            ] = [
                $this->view->messageCount,
                $this->view->recipientAlias,
                $this->view->userRole,
                $this->view->messages
            ] = $accountModel->fetchConversation(
                $alias,
                $page,
                $importantOnly
            );

            if ($message_count) {
                $this->view->MessageCount = $this->User->Info(
                    0,
                    'MessageCount'
                );

                $this->view->recipientPGP = $accountModel->getUserPGP($alias);

                $this->view->trustedVendor =
                    $this->User->IsVendor
                    && $this->User->Attributes['TotalTransacted'] >= AMOUNT_TRANSACTED_TRUSTED_VENDOR;

                return $this->view->render('account/conversations');
            }

            if ($importantOnly) {
                header('Location: ' . URL . 'account/conversation/' . $alias . '/');
                exit;
            }
        }

        if ($recipientID = $accountModel->getUserID($alias)) {
            $this->User->refreshConversation(
                $this->User->ID,
                $recipientID
            );
        }

        header('Location: ' . URL . 'account/conversations/');
        exit;
    }

    public function delete_conversation($alias, $accountModel = false): void
    {
        $this->checkCSRFToken();

        $accountModel = $accountModel ?: $this->loadModel('Account');

        if ($accountModel->deleteConversation($alias)) {
            header('Location: ' . URL . 'account/conversations/');
            exit;
        }
        header('Location: ' . URL . 'account/conversation/' . $alias . '/');
        exit;

    }

    public function report_user($alias): void
    {
        $accountModel = $this->loadModel('Account');

        if ($accountModel->reportUser($alias) && isset($_POST['delete_conversation'])) {
            $this->delete_conversation($alias, $accountModel);
        } else {
            header('Location: ' . URL . 'account/conversation/' . $alias . '/');
            exit;
        }
    }

    public function message($ID): void
    {
        $accountModel = $this->loadModel('Account');

        if ([$userAlias, $messagePage] = $accountModel->findMessage($ID)) {
            header('Location: ' . URL . 'account/conversation/' . $userAlias . '/all/' . $messagePage . '/#message-' . $ID);
            exit;
        }
        header('Location: ' . URL . 'account/conversations/');
        exit;

    }

    public function send_message(): void
    {
        $accountModel = $this->loadModel('Account');

        $doesntNeedFloodCheck =
            $this->User->IsVendor
            || $this->User->IsAdmin
            || $this->User->IsMod;

        if (
            !$doesntNeedFloodCheck
            && !$this->floodCheck('sendMessage', SEND_MESSAGE_MINIMUM_WAIT)
        ) {
            if (
                isset($_POST['is_reply'])
                && $_POST['is_reply'] == '1'
            ) {
                header('Location: ' . URL . 'account/conversation/' . $_POST['recipient_alias'] . '/#reply');
                exit();
            }
            header('Location: ' . URL . 'account/conversations/#new-message');
            exit;

        }

        if ($message_id = $accountModel->sendMessage()) {
            $_SESSION['temp_notifications'][] = [
                'Content' => 'Message sent successfully',
                'Group' => 'Messages',
                'Anchor' => false,
                'Dismiss' => '.',
                'Design' => [
                    'Color' => 'blue',
                    'Icon' => Icon::getClass('CHECK'),
                ],
            ];

            $location =
                (
                    isset($_POST['return'])
                    && preg_match(
                        REGEX_URL_SAFE,
                        $_POST['return']
                    )
                )
                    ? $_POST['return']
                    : 'account/conversation/' . $_POST['recipient_alias'] . '/'; // 'account/message/' . $message_id . '/';

            header('Location: ' . URL . $location);
            exit();
        }
        if (
            isset($_POST['is_reply'])
            && $_POST['is_reply'] == '1'
            && preg_match(REGEX_URL_SAFE, $_POST['recipient_alias'])
        ) {
            header('Location: ' . URL . 'account/conversation/' . $_POST['recipient_alias'] . '/#reply');
            exit();
        }
        header('Location: ' . URL . 'account/conversations/#new-message');
        exit;

    }

    public function toggle_message_important(
        $messageID,
        $returnToImportantTab = false
    ): void {
        $accountModel = $this->loadModel('Account');

        if ($accountModel->toggleMessageImportant($messageID)) {
            if ($returnToImportantTab) {
                [$userAlias, $messagePage] = $accountModel->findMessage($messageID);

                header('Location: ' . URL . 'account/conversation/' . $userAlias . '/important/');
                exit();
            }

            header('Location: ' . URL . 'account/message/' . $messageID . '/');
            exit();
        }

        header('Location: ' . URL . 'account/conversations/');
        exit();
    }

    public function delete_all_messages(): void
    {
        $this->checkCSRFToken();

        $accountModel = $this->loadModel('Account');

        $accountModel->deleteAllMessages();

        header('Location: ' . URL . 'account/conversations/');
        exit();
    }

    public function withdraw_referral_wallet($walletID): void
    {
        $this->checkCSRFToken();

        $accountModel = $this->loadModel('Account');
        $accountModel->withdrawReferralWallet($walletID);

        header('Location: ' . URL . 'account/invites/commissions/');
        exit();
    }

    public function transactions(
        $type = 'ongoing',
        $sort = TRANSACTIONS_DEFAULT_SORTING_MODE,
        $page = 1
    ) {
        if ($this->db->forum) {
            NXS::showError();
        }

        $transactionsModel = $this->loadModel('Transactions');

        $this->view->cryptocurrencyFeeLevelOptions = false;
        switch ($type) {
            case 'expired':
                if (!empty($_POST)) {
                    $this->checkCSRFToken();
                }

                if ($this->view->expiredTransactions = $transactionsModel->getExpiredTransactions()) {
                    return $this->view->render('account/expired_transactions');
                } elseif (
                    !empty($_POST['txIDs'])
                    && count($_POST['txIDs']) == 1
                    && $expiredTXID = $transactionsModel->getTransactionID($_POST['txIDs'][0], $transactionIdentifier)
                ) {
                    $action = $_POST['action-' . $expiredTXID];

                    $destination = false;
                    switch ($action) {
                        case 'extend':
                            $destination = 'finalize';

                            break;
                        case 'dispute':
                            $destination = 'dispute';

                            break;
                        case 'finalize':
                            $destination = 'feedback';

                            break;
                    }

                    header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/' . ($destination ? $destination . '/' : false));
                    exit();
                }
                header('Location: ' . URL . 'account/orders/');
                exit();

                break;
            case 'incipient':
            case 'finalized':
                break;
            default:
                $type = $this->User->IsVendor ? 'sell' : 'buy';

                if ($this->User->IsVendor) {
                    $this->view->cryptocurrencyFeeLevelOptions = $transactionsModel->fetchCryptocurrencyFeeLevels();
                    $this->view->cryptocurrencyFeeLevel =
                        array_key_exists(
                            $this->User->Attributes['Preferences']['CryptocurrencyFeeLevel'],
                            $this->view->cryptocurrencyFeeLevelOptions
                        )
                            ? $this->User->Attributes['Preferences']['CryptocurrencyFeeLevel']
                            : CRYPTOCURRENCIES_CRYPTOCURRENCY_ID_DEFAULT;
                }
        }

        // Preliminary Validation
        $page = (!is_numeric($page) || $page < 1) ? 1 : $page;

        $this->view->type = htmlspecialchars($type);
        $this->view->sortMode = htmlspecialchars($sort);
        $this->view->pageNumber = $page;

        $perPage = TRANSACTIONS_PER_PAGE;
        if (
            $type == 'sell'
            && $advancedView =
                $this->User->Attributes['Preferences']['AdvancedViewOrders'] !== null
                    ? $this->User->Attributes['Preferences']['AdvancedViewOrders']
                    : ORDER_VIEW_ADVANCED_DEFAULT_ENABLED
        ) {
            $perPage = $this->User->Attributes['Preferences']['AdvancedOrdersPerPage'] ?: ORDER_VIEW_ADVANCED_DEFAULT_ITEMS_PER_PAGE;
            $this->view->additionalStylesheets = [
                '/public/css/orders/advanced.css?v1',
            ];
        }
        $this->view->ordersPerPage = $perPage;

        [
            $this->view->transactionCount,
            $transactions
        ] = $transactionsModel->fetchTransactions(
            $type,
            $sort,
            $page,
            $perPage
        );

        $this->view->incipientOrderCount =
            $this->User->IsVendor
                ? $this->User->Info(0, 'IncipientTransactionCount')
                : false;

        if ($type == 'sell') {
            $this->view->inlineStylesheet .= '
				.top-tabs > ul .switch{
					display: inline-flex;
					vertical-align: top;
					width: 150px;
				}
			';
        }
        if ($type == 'finalized') {
            $this->view->inlineStylesheet .= '
				#transaction-table td:nth-child(6){
					text-align: left;
					padding: 10px 0;
				}
				#transaction-table .grey-box {
					line-height: 1.5;
					padding: 10px;
				}
				.cool-table tr:nth-child(2n) .grey-box {
					background-color: #FFF;
				}
			';
        }

        if (
            $this->view->advancedView = $this->view->collapsedView =
                $advancedView
                && $transactions
        ) {
            foreach ($transactions as $i => $transaction) {
                $transactions[$i]['decrypted'] = $transactionsModel->fetchTransaction($transaction['id']);
            }

            $transactionIdentifiers = array_map(
                fn ($transaction) => $transaction['identifier'],
                $transactions
            );
            $this->view->inlineStylesheet .=
                implode(
                    ',',
                    array_map(
                        function ($transactionIdentifier) {
                            return '
								#expand-' . $transactionIdentifier . '-buyer:not(:checked) ~ table #order-' . $transactionIdentifier . ' + tr + tr + tr > td,
								#expand-' . $transactionIdentifier . '-buyer:not(:checked) ~ table #order-' . $transactionIdentifier . ' + tr + tr + tr + tr > td,
								#expand-' . $transactionIdentifier . '-buyer:not(:checked) ~ table #order-' . $transactionIdentifier . ' + tr + tr + tr + tr + tr > td
							';
                        },
                        $transactionIdentifiers
                    )
                ) .
                '{
					font-size: 0;
					opacity: 0;
					line-height: 1;
					padding: 0;
					border: none;
					height: 0;
				}' .
                implode(
                    ',',
                    array_map(
                        fn ($transactionIdentifier) => '#expand-' . $transactionIdentifier . '-buyer:checked ~ table #order-' . $transactionIdentifier . ' + tr + tr .expand-toggle',
                        $transactionIdentifiers
                    )
                ) .
                '{background-color: rgb(0,0,0,.1)}';

        } elseif ($type == 'incipient') {
            $this->view->inlineStylesheet .= '
				#transaction-table td:nth-child(1){
					white-space: nowrap;
					text-align: right;
					font-family: monospace;
					font-size: 12px;
				}
				#transaction-table td:nth-child(3){
					text-align: right;
				}
			';
        } else {
            $this->view->inlineStylesheet .= '
				#transaction-table td:nth-child(1){
					white-space: nowrap;
					text-align: right;
					font-family: monospace;
					font-size: 12px;
				}
				#transaction-table td:nth-child(3) {
					text-align: left;
				}
				#transaction-table td:nth-child(4){
					text-align: right;
				}
				#transaction-table td:nth-child(7){
					padding: 5px 10px;
					text-align: right;
					white-space: nowrap;
				}
			';
        }

        $this->view->transactions = $transactions;
        $this->view->render('account/transactions');
    }

    public function orders(): void
    {
        // ALIAS FOR transactions();
        call_user_func_array(
            [$this, 'transactions'],
            func_get_args()
        );
    }

    public function history($page = 1, $sort = 'desc'): void
    {
        NXS::showError();

        if ($this->User->Info('LogActivities') == 1) {

            $accountModel = $this->loadModel('Account');

            $page = (!is_numeric($page) || $page < 1) ? 1 : $page;

            switch ($sort) {
                case 'asc':
                case 'desc':
                    $sort = $sort;

                    break;
                default:
                    $sort = 'desc';

                    break;
            }

            $this->view->page_number = $page;
            $this->view->sortmode = $sort;

            $this->view->logging = true;

            [$this->view->activity_count, $this->view->activities] = $accountModel->fetchActivities($page, $sort);

        } else {
            $this->view->logging = false;
        }

        $this->view->render('account/history');
    }

    public function favorites($sort = 'rating', $page = 1): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        $accountModel = $this->loadModel('Account');

        if (
            [
                $this->view->listingCount,
                $this->view->listings
            ] = $accountModel->fetchFavoriteListings($sort, $page)
        ) {
            $this->view->pageNumber = htmlspecialchars($page);
            $this->view->sortMode = htmlspecialchars($sort);

            $this->view->favoriteCount = $this->User->Info(0, 'FavoriteListingCount');

            $this->view->render('account/favorites');
        } else {
            header('Location: ' . URL . 'account/');
            exit;
        }
    }

    public function accounting($page = 'invoices', $sortMode = 'id_asc')
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        switch ($sortMode) {
            case 'id_asc':
            case 'id_desc':
            case 'value_asc':
            case 'value_desc':
            case 'date_asc':
            case 'date_desc':
                $this->view->sortMode = $sortMode;

                break;
            default:
                $this->view->sortMode = $sortMode = 'id_asc';
        }

        $accountModel = $this->loadModel('Account');

        $this->view->invoices = $accountModel->fetchInvoices($sortMode);

        return $this->view->render('account/invoices');
    }

    public function referral_commissions($accountModel)
    {
        if ($referralWallets = $accountModel->fetchReferralWallets()) {
            $this->view->hasReferralCommissions = true;
            $this->view->referralWallets = $referralWallets;

            $this->view->inlineStylesheet .= '
				#commissions-table td:nth-child(3){
					font-family: monospace;
					text-align: right;
				}
				#commissions-table td:nth-child(4){
					text-align: right;
				}
			';

            return $this->view->render('account/referral_commissions');
        }

        header('Location: ' . URL . 'account/invites/');
        exit();
    }

    public function invites($type = 'unclaimed', $page = 1)
    {
        $accountModel = $this->loadModel('Account');

        switch ($type) {
            case 'claimed':
                $invites = $accountModel->fetchInvites(true, $page, $inviteCount);

                break;
            case 'commissions':
                return $this->referral_commissions($accountModel);

                break;
                // case 'unclaimed':
            default:
                $type = 'unclaimed';

                if ($this->User->IsVendor) {
                    $accountModel->topUpInvites(INVITES_VENDORS_TOP_UP_QUANTITY);
                } elseif ($this->view->hasInvites) {
                    $accountModel->topUpInvites($this->view->inviteCount, false);
                }

                $invites = $accountModel->fetchInvites(false, $page, $inviteCount);
                $this->view->openRegistration = !$this->db->invite_only;

                $this->view->inlineStylesheet .= '
					#invites-tables td:last-child{
						padding: 5px 10px;
						text-align: right;
						width: 35px;
					}
				';
        }
        $this->view->type = htmlspecialchars($type);
        $this->view->pageNumber = htmlspecialchars($page);
        $this->view->numberOfPages = ceil($inviteCount / INVITES_PER_PAGE);
        $this->view->invites = $invites;
        $this->view->isAmbassador =
            $this->User->IsVendor
            || $isStarBuyer = $this->User->ascertainUserClass(USER_CLASS_ID_STAR_BUYERS);

        $this->view->hasReferralCommissions = $accountModel->hasReferralCommissions();

        return $this->view->render('account/invites');
    }

    public function update_invites($return = 'unclaimed'): void
    {
        $accountModel = $this->loadModel('Account');

        switch ($return) {
            case 'claimed':
                break;
            default: // case 'unclaimed':
                $return = 'unclaimed';
        }

        $accountModel->updateInvites();

        header('Location: ' . URL . 'account/invites/' . $return . '/');
        exit();
    }

    public function listings($sort = 'id', $page = 1, $listing_id = false): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $method_name = substr(__METHOD__, strpos(__METHOD__, '::') + 2);

        $this->view->listing = false;

        $this->view->defaultShipsFrom = false;
        if ($this->User->Attributes['Preferences']['CatalogFilter']['ships_to'] > -1) {
            $shipsTo = $this->User->Attributes['Preferences']['CatalogFilter']['ships_to'] ?: SHIPPING_FILTER_PREFIX_LOCALE . SHIPPING_FILTER_DELIMITER . $this->User->Attributes['Preferences']['LocaleID'];
            [
                $shippingType,
                $shippingID
            ] = explode(
                SHIPPING_FILTER_DELIMITER,
                $shipsTo,
                2
            );

            if ($shippingType == SHIPPING_FILTER_PREFIX_COUNTRY) {
                $this->view->defaultShipsFrom = $shippingID;
            } else {
                $this->view->defaultShipsFrom = $accountModel->_getPrimaryLocaleCountry($shippingID);
            }
        }

        $this->view->hideArchivedListings = $this->User->Attributes['Preferences']['ShowArchivedListings'] == false;

        // $this->view->listings = $sort == 'new' || $sort == 'edit' ? $accountModel->fetchListingIDs() : false;

        switch ($sort) {
            case 'new':
                if (!$this->view->shippingOptions = $accountModel->fetchListingShippingOptions()) {
                    $_SESSION['temp_notifications']['noShippingOption'] = [
                        'Group' => 'Shipping',
                        'Content' => 'You need at least <strong>one</strong> shipping option before you can create listings.',
                        'Design' => [
                            'Color' => 'red',
                            'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                        ],
                    ];
                    header('Location: ' . URL . 'account/shipping/');
                    exit();
                }

                if (!$this->view->listingPaymentMethods = $accountModel->getListingPaymentMethods()) {
                    $_SESSION['temp_notifications']['noShippingOption'] = [
                        'Group' => 'PaymentMethods',
                        'Content' => 'You need at least <strong>one</strong> payment before you can create listings.',
                        'Design' => [
                            'Color' => 'red',
                            'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                        ],
                    ];
                    header('Location: ' . URL . 'account/settings/#crypto');
                    exit();
                }

                $this->view->listingCategories = $accountModel->fetchListingCategories();

                $this->view->units = $accountModel->fetchUnits();

                $this->view->continents = $accountModel->fetchContinentsCountries();

                $this->view->groupingOptions = $accountModel->fetchListingGroupOptions();

                $this->view->render('account/new_listing');

                break;
            case 'edit':
                if (is_numeric($page) && $page > 0) {
                    $listing_id = $page;
                }

                if ($listing = $this->view->listing = $accountModel->fetchListing($listing_id)) {
                    $this->view->listingCategories = $accountModel->fetchListingCategories($listing['content']['category']);
                    $this->view->shippingOptions = $accountModel->fetchListingShippingOptions();
                    $this->view->units = $accountModel->fetchUnits();
                    $this->view->listingPaymentMethods = $accountModel->getListingPaymentMethods($listing_id);

                    // unset($this->view->listings[$listing_id]);

                    // ALLOWED CATEGORIES
                    /*if( $this->view->listing['rating_count'] > 0 ){
                        $this->view->allowedCategories = $_SESSION['edit_listing_allowed_categories'] = NXS::linearArray(
                            NXS::reduceCategories(
                                $this->view->listing['content']['category'],
                                $this->view->listingCategories
                            )
                        );
                    } else */
                    unset($_SESSION['edit_listing_allowed_categories']);

                    // $this->view->listingAttributes = $accountModel->fetchListingAttributes();

                    $this->view->continents = $accountModel->fetchContinentsCountries();

                    $this->view->groupingOptions = $accountModel->fetchListingGroupOptions($listing_id);

                    $this->view->render('account/new_listing');
                } else {
                    header('Location: ' . URL . 'error/');
                    exit();
                }

                break;
            case 'deactivate':
                if (
                    (is_numeric($page) && $page > 0)
                    || $page == 'all'
                ) {
                    $listing_id = $page;
                    $accountModel->toggleListingActive($listing_id, 'DEACTIVATE');
                }

                header('Location: ' . URL . 'account/listings/');
                exit();

                break;
            case 'reactivate':
                if (
                    (is_numeric($page) && $page > 0)
                    || $page == 'all'
                ) {
                    $listing_id = $page;
                    $accountModel->toggleListingActive($listing_id, 'ACTIVATE');
                }

                header('Location: ' . URL . 'account/listings/');
                exit();

                break;
            case 'delete':
                if (is_numeric($page) && $page > 0) {
                    $listing_id = $page;
                    $this->delete_listing($listing_id, $accountModel);
                }

                header('Location: ' . URL . 'account/listings/');
                exit();

                break;
            case 'unarchive':
            case 'archive':
                if (is_numeric($page) && $page > 0) {
                    $listing_id = $page;

                    $accountModel->toggleListingArchived($listing_id);
                }

                header('Location: ' . URL . 'account/listings/');
                exit();

                break;
            case 'copy':
                $_POST['import_listing'] = $page;
                $this->import(false, $accountModel);

                break;
            default:
                $page = (!is_numeric($page) || $page < 1) ? 1 : $page;

                switch ($sort) {
                    // case 'id_asc':
                    case 'id_desc':
                    case 'name_asc':
                    case 'name_desc':
                    case 'price_asc':
                    case 'price_desc':
                    case 'stock_asc':
                    case 'stock_desc':
                        break;
                    default:
                        $sort = 'id_asc';
                }

                $this->view->inlineStylesheet .= '
				#listing-table td > b {
					border-left: solid 3px #DB8700;
					position: absolute;
					top: 0;
					bottom: 0;
					left: 3px;
				}
				#listing-table td:nth-child(1){
					text-align: left;
					white-space: nowrap;
				}
				#listing-table td:nth-child(2){
					text-align: left;
				}
				#listing-table th:nth-child(3),
				#listing-table th:nth-child(4){
					width: 132px;
				}
				#listing-table td:nth-child(7){
					padding: 5px 10px;
					text-align: right;
					width: 100px;
				}
				.text.select > select:empty {
					width: 30px;
					z-index: 3;
				}
				';

                $this->view->pageNumber = $page;
                $this->view->sortMode = $sort;

                if (
                    [$this->view->listingCount, $this->view->listingInactiveCount, $this->view->listings] = $accountModel->fetchListings($sort, $page)
                ) {
                    $this->view->collapsedView = true;
                    $this->view->units = $accountModel->fetchUnits(true);
                }

                $this->view->vendorListingCount = $this->User->Info(0, 'ActiveListingCount');

                $this->view->render('account/listings');
        }
    }

    public function update_listings(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $accountModel->updateListings();

        header('Location: ' . URL . 'account/listings/' . $_POST['sort'] . '/' . $_POST['page'] . '/');
        exit;
    }

    public function delete_listings(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $accountModel->deleteListings();

        header('Location: ' . URL . 'account/listings/' . $_POST['sort'] . '/' . $_POST['page'] . '/');
        exit;
    }

    public function hide_listings(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $accountModel->toggleListingsVisible(false);

        header('Location: ' . URL . 'account/listings/' . $_POST['sort'] . '/' . $_POST['page'] . '/');
        exit;
    }

    public function unhide_listings(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $accountModel->toggleListingsVisible(true);

        header('Location: ' . URL . 'account/listings/' . $_POST['sort'] . '/' . $_POST['page'] . '/');
        exit;
    }

    public function enable_listings(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $accountModel->toggleListingsActive(true);

        header('Location: ' . URL . 'account/listings/' . $_POST['sort'] . '/' . $_POST['page'] . '/');
        exit;
    }

    public function disable_listings(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $accountModel->toggleListingsActive(false);

        header('Location: ' . URL . 'account/listings/' . $_POST['sort'] . '/' . $_POST['page'] . '/');
        exit;
    }

    public function new_listing(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        // HANDLE UPLOAD
        if (!empty($_FILES)) {

            foreach ($_FILES as $key => $value) {
                if (empty($value['name'])) {
                    continue;
                }

                $files[$key] = $accountModel->uploadFile(
                    $key,
                    true,
                    true,
                    false,
                    false,
                    [
                        [
                            'width' => LISTING_IMAGE_WIDTH,
                            'height' => LISTING_IMAGE_HEIGHT,
                            'suffix' => IMAGE_MEDIUM_SUFFIX,
                        ],
                        [
                            'width' => LISTING_IMAGE_THUMBNAIL_WIDTH,
                            'height' => LISTING_IMAGE_THUMBNAIL_HEIGHT,
                            'suffix' => IMAGE_THUMBNAIL_SUFFIX,
                        ],
                    ]
                );
                $validUpload =
                    false !== $files[$key]
                    && empty($files[$key]['error'])
                    && $files[$key]['filepath'] . $files[$key]['filename'] !== 'SS';
                if ($validUpload) {
                    $_POST['uploads'][$key] = $files[$key]['imageID'];
                }
            }
        }

        if ($listing_id = $accountModel->new_listing()) {
            Session::set('listing_feedback', null);
            Session::set('listing_post', null);

            if (
                (
                    isset($_POST['return'])
                    && preg_match(REGEX_URL_SAFE, $_POST['return'])
                )
                || isset($_POST['delete_pic'])
                || isset($_POST['make_pic_primary'])
                || isset($_POST['uploads']['file'])
            ) {
                $location = 'account/listings/edit/' . $listing_id . '/#' . $_POST['return'];
            } else {
                $location = 'i/' . NXS::getB36($listing_id) . '/';
            }

            header('Location: ' . URL . $location);
            exit();
        }
        header('Location: ' . URL . 'account/listings/new/');
        exit();

    }

    public function edit_listing($listing_id): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        // HANDLE UPLOAD
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if (empty($value['name'])) {
                    continue;
                }

                $files[$key] = $accountModel->uploadFile(
                    $key,
                    true,
                    true,
                    false,
                    false,
                    [
                        [
                            'width' => LISTING_IMAGE_WIDTH,
                            'height' => LISTING_IMAGE_HEIGHT,
                            'suffix' => IMAGE_MEDIUM_SUFFIX,
                        ],
                        [
                            'width' => LISTING_IMAGE_THUMBNAIL_WIDTH,
                            'height' => LISTING_IMAGE_THUMBNAIL_HEIGHT,
                            'suffix' => IMAGE_THUMBNAIL_SUFFIX,
                        ],
                    ]
                );

                $validUpload =
                    false !== $files[$key]
                    && empty($files[$key]['error'])
                    && $files[$key]['filepath'] . $files[$key]['filename'] !== '/SS';
                if ($validUpload) {
                    $_POST['uploads'][$key] = $files[$key]['imageID'];
                } else {
                    $_SESSION['temp_notifications']['invalidUpload'] = [
                        'Content' => 'One or more images could not be uploaded. Please try again',
                        'Anchor' => false,
                        'Dismiss' => '.',
                        'Group' => 'Specific',
                        'Design' => [
                            'Color' => 'red',
                            'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
                        ],
                    ];
                }
            }
        }

        if ($accountModel->edit_listing($listing_id)) {
            unset($_SESSION['listing_feedback'], $_SESSION['listing_post'], $_SESSION['edit_listing_allowed_categories']);
            if (
                (
                    isset($_POST['return'])
                    && preg_match(REGEX_URL_SAFE, $_POST['return'])
                )
                || (
                    (
                        !empty($_POST['delete_pic'])
                        || !empty($_POST['make_pic_primary'])
                    )
                    && $_POST['return'] = 'picture-options'
                )
            ) {
                $location = 'account/listings/edit/' . $listing_id . '/#' . $_POST['return'];
            } else {
                $location = 'account/listings/';
            }
            header('Location: ' . URL . $location);
            exit();
        }
        header('Location: ' . URL . 'account/listings/edit/' . $listing_id . '/');
        exit();

    }

    public function delete_listing($listing_id = false, $accountModel = false): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        if (!class_exists('templateView')) {
            parent::__construct('narrow', true);
        }

        if (!$accountModel) {
            $accountModel = $this->loadModel('Account');
        }

        if (!is_numeric($listing_id) && $listing_id < 0) {
            header('Location: ' . URL . 'account/listings/');
            exit;
        }

        $accountModel->deleteListing($listing_id);

        header('Location: ' . URL . 'account/listings/');
        exit();
    }

    public function toggle_stealth($listing_id): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $accountModel->toggleStealth($listing_id);

        header('Location: ' . URL . 'account/listings/');
        exit;
    }

    public function import($target_listing_id = false, $accountModel = false): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        if (!$accountModel) {
            $accountModel = $this->loadModel('Account');
        }

        $imported_listing_id = $_POST['import_listing'];

        $target_listing = $this->view->targetListing = $target_listing_id ? $accountModel->fetchListing($target_listing_id) : false;

        if ($this->view->listing = $accountModel->fetchListing($imported_listing_id)) {
            $this->view->listings = $accountModel->fetchListingIDs();

            unset(
                $this->view->listings[$imported_listing_id],
                $this->view->listing['content']['promo_code_ids']
            );

            if ($target_listing) {
                unset($this->view->listings[$target_listing['id']]);
            }

            $this->view->listingCategories = $accountModel->fetchListingCategories($target_listing ? $target_listing['content']['category'] : false);
            $this->view->shippingOptions = $accountModel->fetchListingShippingOptions();
            $this->view->units = $accountModel->fetchUnits();
            $this->view->listingPaymentMethods = $accountModel->getListingPaymentMethods($imported_listing_id);

            $this->view->listing['content']['category'] = $target_listing ? $target_listing['content']['category'] : $this->view->listing['content']['category'];

            $this->view->listing['content']['name'] = $target_listing ? $target_listing['content']['name'] : '';
            $this->view->listing['content']['canChangeTitle'] = true;

            $this->view->listing['id'] = $target_listing ? $target_listing['id'] : false;

            // ALLOWED CATEGORIES
            /*if( $target_listing && $target_listing['rating_count'] > 0 ){
                $this->view->allowedCategories = $_SESSION['edit_listing_allowed_categories'] = NXS::linearArray(
                        NXS::reduceCategories(
                            $target_listing['content']['category'],
                            $this->view->listingCategories
                        )
                    );
            } else */
            unset($_SESSION['edit_listing_allowed_categories']);

            $this->view->continents = $accountModel->fetchContinentsCountries();

            $this->view->groupingOptions = $accountModel->fetchListingGroupOptions($imported_listing_id, false);

            $this->view->isImport = true;

            $this->view->render('account/new_listing');
        } else {
            header('Location: ' . URL . 'account/listings/');
            exit();
        }
    }

    public function shipping($action = false): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $this->view->shippingOptions = $accountModel->fetchShippingOptions();

        if ($this->view->newShippingOption = $action == 'new') {
            if (!$this->view->shippingOptions) {
                $this->view->shippingOptions = [];
            }
            $this->view->shippingOptions[] = [
                'ID' => false,
                'Name' => false,
                'Description' => false,
                'Price' => '0.00',
                'CurrencyID' => $this->User->Currency['ID'],
            ];
        }

        $this->view->render('account/shipping');
    }

    public function update_shipping_options(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $accountModel->updateShippingOptions();

        header('Location: ' . URL . 'account/shipping/' . (isset($_POST['save_and_insert']) ? 'new/' : false));
        exit;
    }

    public function profile($action = false): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        [$this->view->description, $this->view->sections] = $accountModel->fetchVendorProfile();

        if (
            ($this->view->new_section = $action == 'new')
            && (
                !$this->view->sections
                || count($this->view->sections) < MAX_VENDOR_SECTIONS
            )
        ) {
            if (!$this->view->sections) {
                $this->view->sections = [];
            }
            $this->view->sections[] = [
                'id' => false,
                'name' => false,
                'content' => false,
                'type' => false,
            ];
        }

        $this->view->render('account/profile');
    }

    public function update_sections(): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $this->checkCSRFToken();

        $accountModel = $this->loadModel('Account');
        $accountModel->updateSections();

        header('Location: ' . URL . 'account/profile/' . (isset($_POST['save_and_insert']) ? 'new/' : false));
        exit;
    }

    public function ask_question($listing_id): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        $accountModel = $this->loadModel('Account');

        if (
            $this->floodCheck('askQuestion', ASK_QUESTION_MINIMUM_WAIT)
            && $accountModel->askQuestion($listing_id)
        ) {
            header('Location: ' . (isset($_POST['prefix']) && preg_match('/[\w_]{3,20}/', $_POST['prefix']) ? 'http://' . $_POST['prefix'] . '.' . substr(URL, 7) : URL) . 'i/' . NXS::getB36($listing_id) . '/#questions');
            exit;
        }

        header('Location: ' . URL . 'account/');
        exit;
    }

    public function answer_question($question_id): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        if ([$success, $listing_id] = $accountModel->answerQuestion($question_id)) {
            if ($success) {
                header('Location: ' . URL . 'i/' . NXS::getB36($listing_id) . '/#questions');
                exit;
            }
            header('Location: ' . URL . 'i/' . NXS::getB36($listing_id) . '/?modal=answer-question' . $question_id . '#questions');
            exit;

        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Question could not be found or has been deleted.',
            'Design' => [
                'Color' => 'blue',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'account/listings/');
        exit();

    }

    public function delete_question($question_id): void
    {
        if ($this->db->forum) {
            NXS::showError();
        }

        if (!$this->User->IsVendor) {
            header('Location: ' . URL . 'account/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        if ([$success, $listing_id] = $accountModel->deleteQuestion($question_id)) {
            header('Location: ' . URL . 'i/' . NXS::getB36($listing_id) . '/#questions');
            exit;
        }
        $_SESSION['temp_notifications'][] = [
            'Content' => 'Question could not be found or has been deleted.',
            'Design' => [
                'Color' => 'blue',
                'Icon' => Icon::getClass('EXCLAMATION_MARK_IN_CIRCLE'),
            ],
        ];

        header('Location: ' . URL . 'account/listings/');
        exit();

    }

    public function find_pending_deposit(): void
    {
        $accountModel = $this->loadModel('Account');
        $transactionsModel = $this->loadModel('Transactions');

        if ($TXID = $accountModel->getPendingDepositTXID()) {
            $transactionIdentifier = $transactionsModel->getTransactionIdentifier($TXID);
            header('Location: ' . URL . 'tx/' . $transactionIdentifier . '/pay/#pay');
            exit;
        }
        header('Location: ' . URL . 'account/orders/');
        exit;

    }
}
