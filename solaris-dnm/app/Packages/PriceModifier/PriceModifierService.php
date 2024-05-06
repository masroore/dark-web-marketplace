<?php

namespace App\Packages\PriceModifier;

class PriceModifierService
{
    public const REFERRAL_MODIFIER = ReferralPriceModifier::class;
    public const PROMOCODE_MODIFIER = PromocodePriceModifier::class;
    public const GROUP_MODIFIER = GroupPriceModifier::class;

    public function apply($price, $currency, $modifiers = [], $arguments = [])
    {
        $resultPrice = $price;
        foreach ($modifiers as $modifier) {
            $modifierInstance = new $modifier();
            if ($modifierInstance instanceof IPriceModifier) {
                $resultPrice = $modifierInstance->applyModifier($resultPrice, $currency, $arguments);
            }
        }

        return $resultPrice;
    }
}
