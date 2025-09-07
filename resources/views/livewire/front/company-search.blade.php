<div wire:key="company-search">
    <div class="input-group">
        <span class="input-group-text" id="search-addon">🔎</span>
        <input type="text" class="form-control" placeholder="{{ __('company.search_placeholder') }}" aria-label="Search" aria-describedby="search-addon" wire:model.debounce.300ms="q">
    </div>

    @if(!empty($q))
        <div class="list-group mt-2 shadow-sm">
            @forelse($results as $r)
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ localized_route('companies.show', ['company' => $r]) }}">
                    <span>{{ $r->name }}</span>
                    <small class="text-muted">{{ $r->oib }}</small>
                </a>
            @empty
                <div class="list-group-item text-muted">{{ __('company.no_results') }}</div>
            @endforelse
        </div>
    @endif
</div>
