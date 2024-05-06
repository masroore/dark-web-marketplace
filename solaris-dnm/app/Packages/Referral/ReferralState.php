<?php

namespace App\Packages\Referral;

use App\Shop;
use App\User;

class ReferralState
{
    public $isEnabled = false;

    // See: App\Http\Middleware\UpdateReferralState
    public $isReferralUrl = false;

    /** @var float */
    public $fee = 0;

    /** @var User */
    public $invitedBy;

    public function __construct()
    {
        $this->isEnabled = (bool) Shop::getDefaultShop()->referral_enabled;
    }
}
