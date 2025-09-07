<?php

namespace App\Listeners;

use App\Events\DailySessionCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ActivateCompanyLink
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
    public function handle(DailySessionCompleted $e): void
    {
        $e->company->forceFill(['is_link_active' => true])->save();
        // opcionalno: mail/notification
    }
}