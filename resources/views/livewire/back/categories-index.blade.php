<div class="p-6" wire:key="back-categories-tree">
    <h1 class="text-2xl font-bold mb-4">{{ __('company.admin_categories') }}</h1>

    <form wire:submit.prevent="create" class="flex gap-2 mb-4">
        <input type="text" wire:model="name" required
               placeholder="{{ __('company.category_name') }}"
               class="border rounded px-3 py-2">
        <select wire:model="parentId" class="border rounded px-3 py-2">
            <option value="">{{ __('company.no_parent') }}</option>
            @foreach($tree as $node)
                @include('back.partials.category-option', ['node' => $node, 'prefix' => ''])
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
            {{ __('company.add_category') }}
        </button>
    </form>

    <div class="bg-white border rounded p-4">
        @foreach($tree as $node)
            @include('back.partials.category-node', ['node' => $node])
        @endforeach
    </div>
</div>
