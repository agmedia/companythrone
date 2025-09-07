<?php

namespace App\Livewire\Front;

use App\Models\Back\Catalog\Company;
use Livewire\Component;

class CompanySearch extends Component
{

    public string $q = '';


    public function render()
    {
        $results = [];
        if (strlen($this->q) >= 2) {
            $results = Company::query()
                              ->where('name', 'like', "%{$this->q}%")
                              ->orWhere('oib', 'like', "%{$this->q}%")
                              ->limit(10)->get();
        }

        return view('livewire.front.company-search', compact('results'));
    }
}
