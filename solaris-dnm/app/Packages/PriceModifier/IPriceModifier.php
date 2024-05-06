<?php

namespace App\Packages\PriceModifier;

interface IPriceModifier
{
    public function applyModifier($price, $currency, $arguments = []);
}
