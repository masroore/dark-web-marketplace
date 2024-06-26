<?php

namespace App\Traits;

use App\Jobs\BitmessageNotify;

trait Notifiable
{
    /**
     * Create new notifications for specified user.
     */
    public function notify(string $content, ?string $routeName = null, ?string $routePramas = null): void
    {
        $this->notifications()->create(['description' => $content, 'route_name' => $routeName, 'route_params' => $routePramas]);

        /**
         * Bitmessage.
         */
        if (config('bitmessage.enabled')) {
            if ($this->bitmessage_address !== null) {
                // if its enabled send message
                BitmessageNotify::dispatch('Notification from marketplace', $content, $this->bitmessage_address)->delay(now()->addSecond(1));
            }
        }
    }

    /**
     * Return user's unread notifications.
     *
     * @return mixed
     */
    public function unreadNotifications()
    {
        return $this->notifications()->where('read', 0);
    }
}
