<div class="p-6" wire:key="back-companies-index">
    <h1 class="text-2xl font-bold mb-4">{{ __('company.admin_companies') }}</h1>

    <div class="flex gap-3 mb-4">
        <input type="text" wire:model.debounce.300ms="q"
               placeholder="{{ __('company.search_placeholder') }}"
               class="border rounded px-3 py-2 w-1/2">
        <select wire:model="level" class="border rounded px-3 py-2">
            <option value="">{{ __('company.all_levels') }}</option>
            @foreach(range(1,5) as $n)
                <option value="{{ $n }}">Level {{ $n }}</option>
            @endforeach
        </select>
    </div>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full">
            <thead class="bg-gray-100">
            <tr>
                <th class="text-left px-3 py-2">#</th>
                <th class="text-left px-3 py-2">{{ __('company.name') }}</th>
                <th class="text-left px-3 py-2">Level</th>
                <th class="text-left px-3 py-2">{{ __('company.email') }}</th>
                <th class="text-left px-3 py-2">{{ __('company.status') }}</th>
                <th class="text-right px-3 py-2">{{ __('company.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($companies as $c)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $c->id }}</td>
                    <td class="px-3 py-2">
                        <a href="{{ localized_route('companies.show', ['company' => $c]) }}" class="text-blue-600 hover:underline">
                            {{ $c->name }}
                        </a>
                    </td>
                    <td class="px-3 py-2">{{ $c->level?->number ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $c->email }}</td>
                    <td class="px-3 py-2">
              <span class="px-2 py-1 rounded text-xs
                {{ $c->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $c->is_published ? __('company.published') : __('company.unpublished') }}
              </span>
                    </td>
                    <td class="px-3 py-2 text-right">
                        @can('update', $c)
                            <a href="{{ localized_route('companies.show', ['company' => $c]) }}" class="text-sm text-blue-600 hover:underline">
                                {{ __('company.view') }}
                            </a>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $companies->links() }}
    </div>
</div>
