@php
    $slots = is_array($session->slots_payload)
             ? ($session->slots_payload['slots'] ?? [])
             : (json_decode($session->slots_payload, true)['slots'] ?? []);
    $completed = (int)($session->completed_count ?? 0);
@endphp

<div class="daily-buttons space-y-4" wire:key="daily-buttons-{{ $session->id }}">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">{{ __('company.daily_tasks') }}</h3>
        <div class="text-sm">
            {{ __('company.progress') }}:
            <strong>{{ $completed }}/25</strong>
            @if($session->completed_25)
                <span class="ml-2 inline-block px-2 py-1 text-green-700 bg-green-100 rounded">{{ __('company.completed') }}</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-5 gap-2">
        @foreach($slots as $slot)
            <a href="{{ $slot['url'] ?? '#' }}"
               target="_blank" rel="nofollow noopener"
               class="block text-center px-3 py-4 rounded border
                @if($completed >= ($slot['slot'] ?? 0)) bg-green-600 text-white border-green-600
                @else bg-white hover:bg-gray-50 border-gray-300 @endif">
                {{ $slot['slot'] ?? '' }}
                @if(isset($slot['level']))
                    <div class="text-[10px] opacity-70">{{ __('company.level') }} {{ $slot['level'] }}</div>
                @endif
            </a>
        @endforeach
    </div>

    @if(!$session->completed_25)
        <p class="text-xs text-gray-500">
            {{ __('company.link_activation_hint') }}
        </p>
    @endif
</div>
