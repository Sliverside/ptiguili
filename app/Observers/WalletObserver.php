<?php

namespace App\Observers;

use App\Models\Wallet;
use Carbon\Carbon;

class WalletObserver
{
    /**
     * Handle the Wallet "retrieved" event.
     *
     * @return void
     */
    public function retrieved(Wallet $wallet)
    {
        $now = Carbon::now();
        $daysFromLastUpdate = $wallet->coins_update_at->diffInDays($now);
        if ($daysFromLastUpdate <= 0) {
            return;
        }

        $wallet->coins_update_at = $now;
        $wallet->coins += $daysFromLastUpdate;
        $wallet->update();
    }
}
