<?php

namespace App\Livewire\Back;

use Livewire\Component;
use App\Models\Back\Catalog\Category;

class CategoriesTree extends Component
{

    public string $name = '';

    public ?int $parentId = null;


    public function create()
    {
        $this->authorize('manage', Category::class);
        $cat = Category::firstOrCreate(['name' => $this->name]);
        if ($this->parentId) {
            $cat->appendToNode(Category::find($this->parentId))->save();
        }
        $this->reset(['name', 'parentId']);
    }


    public function render()
    {
        return view('livewire.back.categories-index', [
            'tree' => Category::defaultOrder()->get()->toTree(),
        ]);
    }
}
