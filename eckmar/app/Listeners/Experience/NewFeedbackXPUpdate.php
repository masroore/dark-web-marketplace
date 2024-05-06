<?php

namespace App\Listeners\Experience;

use App\Events\Purchase\NewFeedback;

class NewFeedbackXPUpdate
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(NewFeedback $event): void
    {
        $feedback = $event->feedback;
        $vendor = $feedback->vendor;
        $valueMultiplier = config('experience.multipliers.feedback_per_usd');
        $starMultiplier = config('experience.multipliers.feedback_per_star');
        $totalStars = $feedback->quality_rate + $feedback->communication_rate + $feedback->shipping_rate;
        $totalXP = $feedback->product_value * $valueMultiplier + $totalStars * $starMultiplier;

        /**
         * Add experience.
         */
        if ($feedback->type == 'positive') {

            $vendor->grantExperience(round($totalXP));
        }
        if ($feedback->type == 'negative') {
            $vendor->takeExperience(round($totalXP));
        }
    }
}
