<?php

/**
 * Class Cron.
 */
class cron extends Controller
{
    public function __call($name, $arguments): void
    {
        set_time_limit(0);
        call_user_func_array(
            [
                $this,
                'run',
            ],
            array_merge([$name], $arguments)
        );
    }

    public function __construct()
    {
        global $argv;

        if (!isset($argv)) {
            return NXS::showError();
        }

        parent::__construct(false, false, false, false);
    }

    public function run()
    {
        $args = func_get_args();
        $schedule = array_shift($args);

        if ($schedule !== 'test') {
            $cronModel = $this->loadModel('Cron');
            $cronModel->getRunClearance();
        }

        switch ($schedule) {
            case CRON_SCHEDULE_EVERY_MINUTE:
                $this->checkPendingDepositTransactions($transactionsModel);

                break;
            case CRON_SCHEDULE_EVERY_2_MINUTES:
                // $this->updateUserInfoCaches($cronModel);
                break;
            case CRON_SCHEDULE_EVERY_10_MINUTES:
                $this->checkElectrumServers($cronModel);
                $this->checkUsersOnline($cronModel);

                break;
            case CRON_SCHEDULE_EVERY_15_MINUTES:
                sleep(mt_rand(0, 10 * 60));
                $this->broadcastTransactions($transactionsModel);
                $this->updateExchangeRates($cronModel);
                $this->processReferralWalletWithdrawals($transactionsModel);

                break;
            case CRON_SCHEDULE_EVERY_30_MINUTES:
                $this->setTransactionsExpired($transactionsModel);
                $this->autofinalizeTransactions($transactionsModel);
                $this->refundUnacceptedTransactions($transactionsModel);
                $this->autoVacationVendors($cronModel);
                $this->decrementBuyerNotifications($transactionsModel);
                $this->ascertainFailedTransactionDeposits(
                    FAILED_DEPOSIT_ASCERTAINMENT_WINDOW_MINUTES,
                    $transactionsModel
                );
                $this->checkUnspentOutputs($transactionsModel);

                break;
            case CRON_SCHEDULE_EVERY_HOUR:
                $this->checkPendingDepositConfirmationTransactions($transactionsModel);
                $this->checkUnconfirmedRejectedTransactions($transactionsModel);
                $this->updateCryptocurrencyNetworkFeeEstimates($transactionsModel);
                $this->insertDailyBTCRate($cronModel);
                $this->insertDailyLTCRate($cronModel);

                break;
            case CRON_SCHEDULE_EVERY_DAY:
                // $this->awardForumRank($cronModel);
                // $this->appointStarMembers($cronModel);
                $this->deleteMessages($cronModel);
                $this->deleteUnnecessaryData($cronModel);
                $this->ascertainFailedTransactionDeposits(
                    FAILED_DEPOSIT_ASCERTAINMENT_WINDOW_MINUTES_EXTENDED,
                    $transactionsModel
                );

                break;
                /*case CRON_METHOD_GET_USER_INFO:
                    $this->getUserInfo($args[0], $cronModel);
                break;*/
            case 'test':
                $cryptocurrency = $this->User->getCryptocurrency(1);

                $rs = '5221031396d54eb7c53a00f78338b7e09efb1a12bba0dec296ffac8ab6dbe3e78e35c921039debce35fa0eaca0b5ee749d0cc3dfb6ac305b9986e8845b623b32542b64044652ae';

                var_dump(
                    $cryptocurrency->encodeRedeemscript($rs, true)
                );

                exit;
                // Generate Combinations
                $combinations = NXS::enumerateCombinations(30, 3);

                foreach ($combinations as $combination) {
                    $combinationID = $this->db->qQuery('INSERT INTO `Combination` VALUES ()');

                    foreach ($combination as $combinationElement) {
                        $this->db->qQuery(
                            '
								INSERT INTO
									`CombinationElement` (
										`CombinationID`,
										`Element`
									)
								VALUES (
									?,
									?
								)
							',
                            'ii',
                            [
                                $combinationID,
                                $combinationElement,
                            ]
                        );
                    }
                }

                // $this->setTransactionsExpired($transactionsModel);
                // $this->refundUnacceptedTransactions($transactionsModel);

                // $this->updateExchangeRates($cronModel);

                /*$this->ascertainFailedTransactionDeposits(
                    FAILED_DEPOSIT_ASCERTAINMENT_WINDOW_MINUTES,
                    $transactionsModel
                );*/

                // $this->refreshConversations();

                /*$initialTime = time();

                $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

                var_dump(
                    $transactionsModel->getAddressBalance(
                        1,
                        '1LQzk7Z4pP1YBTMpUWVf4Lbcu8ZuEMshTh'
                    ),
                    $transactionsModel->getAddressBalance(
                        1,
                        '1EDWkKgwh3EbBjCHEpDGRh3LE6vWHPSfjA'
                    ),
                    $transactionsModel->getAddressBalance(
                        1,
                        '1NEsMmAApRTCmJLaHDEC7cB4yUAidAk8ao'
                    ),
                    $transactionsModel->getAddressBalance(
                        1,
                        '18ruRqaDr7BU3s78ch9sfUs3ez7qR1kL7w'
                    ),
                    $transactionsModel->getAddressBalance(
                        1,
                        '1EfnGqbHYEPXzwwwNqtfn29QfLTirnE7Xu'
                    ),
                    $transactionsModel->getAddressBalance(
                        1,
                        '1BFRWr2pMxip611UywEMbkeiXV2Q6DXsV8'
                    )
                );

                echo time() - $initialTime;
                die;
                */

                // $this->checkPendingDepositTransactions($transactionsModel);
                // $this->ascertainFailedTransactionDeposits(
                //	FAILED_DEPOSIT_ASCERTAINMENT_WINDOW_MINUTES,
                //	$transactionsModel
                // );

                // $this->checkUnconfirmedRejectedTransactions($transactionsModel);
                // $this->checkPendingDepositConfirmationTransactions($transactionsModel);
                // $this->updateUserInfoCaches($cronModel);
                // $this->insertDailyBTCRate($cronModel);
                // $this->broadcastTransactions($transactionsModel);
                // $this->updateExchangeRates($cronModel);
                // $this->deleteUnnecessaryData($cronModel);
                // $this->autoVacationVendors($cronModel);
                // $this->pokeElectrumDaemon();
                // $this->insertDailyBTCRate();
                // $this->deleteMessages($cronModel);
                // $this->appointStarMembers($cronModel);

                // $this->updateCryptocurrencyNetworkFeeEstimates($transactionsModel);

                // $this->processReferralWalletWithdrawals($transactionsModel);

                /*$images = $this->db->qSelect(
                    "
                        SELECT DISTINCT
                            `Image`.`ID`,
                            parentImage.`File`
                        FROM
                            `Image`
                        INNER JOIN
                            `Image` parentImage ON
                                `Image`.`OriginalID` = parentImage.`ID`
                        INNER JOIN
                            `Listing_Image` ON
                                `ImageID` = parentImage.`ID`
                        WHERE
                            Image.Filename REGEXP '_medium\\\.\\\w+$'
                    "
                );

                foreach ($images as $image){
                    $imagick = new Imagick();
                    $imagick->readImageBlob($image['File']);

                    $imageDimensions = $imagick->getImageGeometry();

                    $width = LISTING_IMAGE_WIDTH;
                    $height = LISTING_IMAGE_HEIGHT;

                    $img_height = $imageDimensions['height'];
                    $img_width = $imageDimensions['width'];

                    if(
                        (
                            $width &&
                            $img_width > $width
                        ) ||
                        (
                            $height &&
                            $img_height > $height
                        )
                    ){
                        if(
                            !$height ||
                            (
                                $width &&
                                $img_width/$img_height < $width/$height
                            )
                        )
                            $imagick->thumbnailImage($width, 0);
                        else
                            $imagick->thumbnailImage(0, $height);
                    }

                    $imagick->stripImage();

                    $stmt = $this->db->prepare(
                        "
                            UPDATE
                                `Image`
                            SET
                                `File` = ?
                            WHERE
                                `ID` = ?
                        "
                    );

                    $null = NULL;
                    $stmt->bind_param("bi", $null, $image['ID']);
                    $stmt->send_long_data(
                        0,
                        $imagick->getImageBlob()
                    );

                    $stmt->execute();
                }*/
                break;
        }

        return true;
    }

    public function refreshConversations(): void
    {
        echo 'Refreshing Conversations';

        if (
            $conversations = $this->db->qSelect(
                '
					SELECT	`ID`
					FROM	`Conversation`
				'
            )
        ) {
            foreach ($conversations as $conversation) {
                $this->User->refreshConversation($conversation['ID']);
            }
        }
    }

    public function checkUsersOnline(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->checkUsersOnline();
    }

    public function checkElectrumServers(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->checkElectrumServers();
    }

    public function awardForumRank(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->awardForumRank();
    }

    public function appointStarMembers(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->appointStarMembers();
    }

    public function autoVacationVendors(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->autoVacationVendors();
    }

    public function deleteUnnecessaryData(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        $cronModel->deleteTransactionData();
        $cronModel->deleteUserContent();
        $cronModel->deleteImages();

        return true;
    }

    public function updateExchangeRates(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->updateExchangeRates();
    }

    public function insertDailyBTCRate(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->insertDailyBTCRate();
    }

    public function insertDailyLTCRate(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->insertDailyLTCRate();
    }

    public function checkUnspentOutputs(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->checkUnspentOutputs();
    }

    public function processReferralWalletWithdrawals(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->processReferralWalletWithdrawals();
    }

    public function updateCryptocurrencyNetworkFeeEstimates(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->updateCryptocurrencyNetworkFeeEstimates();
    }

    public function setTransactionsExpired(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->setTransactionsExpired();
    }

    public function decrementBuyerNotifications(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->decrementBuyerNotifications();
    }

    public function autofinalizeTransactions(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->autofinalizeTransactions();
    }

    public function refundUnacceptedTransactions(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->refundUnacceptedTransactions();
    }

    public function broadcastTransactions(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->broadcastTransactions();
    }

    public function getUserInfo(
        $userID,
        &$cronModel = false
    ) {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->getUserInfo($userID);
    }

    public function updateUserInfoCaches(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->updateUserInfoCaches();
    }

    public function checkUnconfirmedRejectedTransactions(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->checkUnconfirmedRejectedTransactions();
    }

    public function deleteMessages(&$cronModel = false)
    {
        $cronModel = $cronModel ?: $this->loadModel('Cron');

        return $cronModel->deleteMessages();
    }

    public function ascertainFailedTransactionDeposits(
        $ascertainmentWindow,
        &$transactionsModel = false
    ) {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->ascertainFailedDeposits($ascertainmentWindow);
    }

    public function checkPendingDepositTransactions(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->checkTransactionDeposits(true);
    }

    public function checkPendingDepositConfirmationTransactions(&$transactionsModel = false)
    {
        $transactionsModel = $transactionsModel ?: $this->loadModel('Transactions');

        return $transactionsModel->checkTransactionDepositConfirmations();
    }
}
