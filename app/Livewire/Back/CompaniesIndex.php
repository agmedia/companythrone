<?php

namespace App\Livewire\Back;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Back\Catalog\Company;

class CompaniesIndex extends Component
{

    use WithPagination;

    public string $q = '';

    public ?int $level = null;


    public function render()
    {
        $query = Company::query()->with('level');

        if ($this->q) {
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$this->q}%")
                ->orWhere('oib', 'like', "%{$this->q}%")
                ->orWhere('email', 'like', "%{$this->q}%"));
        }
        if ($this->level) {
            $query->whereHas('level', fn($q) => $q->where('number', $this->level));
        }

        return view('livewire.back.companies-index', [
            'companies' => $query->paginate(20),
        ]);
    }
}

