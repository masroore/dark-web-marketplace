<?php

/**
 * Class Admin.
 */
class api extends Controller
{
    public function __construct()
    {
        parent::__construct(false, true, false, true);
    }

    public function update_user_prefs(
        $preference,
        $value
    ): void {
        $newPrefs = false;
        switch ($preference) {
            case 'LiveUpdate':
                $newPrefs['LiveUpdate'] = $value == 1;

                break;
            case 'EnableSound':
                $newPrefs['EnableSound'] = $value == 1;

                break;
        }

        if (
            $newPrefs
            && $newPrefs != $this->User->Attributes['Preferences']
        ) {
            $this->User->updatePrefs($newPrefs);
        }

        exit('Done');
    }

    public function fetch_user_notifications(): void
    {
        $accountModel = $this->loadModel('Account');

        $accountModel->getDashboardNotifications();

        echo json_encode(
            [
                'messages' => $this->User->Info('MessageCount'),
                'notifications' => array_merge(
                    $this->User->Notifications->all['Vendor'],
                    $this->User->Notifications->all['Dashboard']
                ),
            ]
        );
        exit;
    }

    public function query_chat_messages($chatID = false): void
    {
        if (!$this->User->IsAdmin && !$this->User->IsMod) {
            header('Location: ' . URL . 'error/');
            exit;
        }

        $accountModel = $this->loadModel('Account');

        $chatMessages = $accountModel->fetchChatMessages(
            $chatID,
            CHAT_MESSAGES_SORT_MODE_DEFAULT,
            API_QUERY_CHAT_MESSAGES_QUANTITY,
            0,
            true,
            true,
            true
        );

        echo json_encode($chatMessages);
        exit;
    }
}
