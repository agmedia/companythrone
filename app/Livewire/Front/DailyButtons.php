<?php

namespace App\Livewire\Front;

use App\Models\Back\Catalog\Company;
use App\Models\Shared\DailySession;
use Carbon\Carbon;
use Livewire\Component;

class DailyButtons extends Component
{

    public Company $company;


    public function mount(Company $company)
    {
        $this->company = $company;
    }


    public function render()
    {
        $today   = Carbon::today();
        $session = DailySession::firstOrCreate([
            'company_id' => $this->company->id, 'day' => $today
        ], ['slots_payload' => json_encode([])]);

        return view('livewire.front.daily-buttons', ['session' => $session]);
    }
}
