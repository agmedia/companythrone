<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\InactivityReminderMail;
use App\Services\Settings\SettingsManager;

class SendInactivityReminders extends Command
{
    protected $signature = 'reminders:send-inactivity';
    protected $description = 'Pošalji podsjetnike korisnicima koji nisu bili aktivni 5 dana';

    public function handle(): void
    {
        $settings = app(SettingsManager::class);

        if (!$settings->get('company', 'send_inactivity_reminders')) {
            $this->info('Podsjetnici su isključeni u postavkama.');
            return;
        }

        $cutoff = now()->subDays(5);

        $users = User::where('daily_reminder_opt_out', false)
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_reminder_sent')
                    ->orWhere('last_reminder_sent', '<', $cutoff);
            })
            ->where(function ($q) use ($cutoff) {
                $q->whereDoesntHave('clicks', function ($sub) use ($cutoff) {
                    $sub->where('day', '>=', $cutoff->toDateString());
                });
            })
            ->get();

        if ($users->isEmpty()) {
            $this->info('Nema korisnika za podsjetiti.');
            return;
        }

        foreach ($users as $user) {
            Mail::to($user->email)->send(new InactivityReminderMail($user));
            $user->last_reminder_sent = now();
            $user->save();
            $this->info("Poslan podsjetnik korisniku: {$user->email}");
        }
    }
}
