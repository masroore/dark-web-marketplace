<?php

namespace App\Services;

use App\Integrations\CryptoPaymentPlatform\ApiClient;
use App\User;
use Illuminate\Support\Facades\Cache;

class CryptoPaymentPlatformService
{
    public const PAGE_LIMIT = 10;

    private ApiClient $client;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->client = app(ApiClient::class);
    }

    public function getHistory(int $page = 0, bool $cached = false): array
    {
        if (!$cached) {
            $history = $this->client->getHistory($this->user->wallet_id, self::PAGE_LIMIT * $page, self::PAGE_LIMIT);
        } else {
            $history = Cache::get('history_' . $this->user->wallet_id . '_' . $page, []);
            if (!$history) {
                $history = $this->client->getHistory($this->user->wallet_id, self::PAGE_LIMIT * $page, self::PAGE_LIMIT);
                Cache::put('history_' . $this->user->wallet_id . '_' . $page, $history, 350);
            }
        }

        $total = $history['total'] ?? 0;

        $result = [
            'result' => $history['result'] ?? [],
            'pages' => ceil($total / self::PAGE_LIMIT),
        ];

        return $result;
    }
}
