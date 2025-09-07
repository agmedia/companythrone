<?php

namespace App\Listeners;

use App\Events\SubscriptionExpired;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeactivateCompanyOnExpiry
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }


    /**
     * Handle the event.
     */
    public function handle(SubscriptionExpired $e): void
    {
        $e->company->forceFill(['is_link_active' => false])->save();
        // poslati ponudu/obnovu
    }
}
