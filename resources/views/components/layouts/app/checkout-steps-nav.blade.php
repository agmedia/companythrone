@php
    // 1) definicija koraka (label + ikona + imena ruta koje pripadaju koraku)
    $steps = [
        [
            'label' => __('Dodaj tvrtku'),
            'icon'  => 'fi-map',
            'routes'=> ['companies.create','companies.store'],
        ],
        [
            'label' => __('Odabir plaćanja'),
            'icon'  => 'fi-swatches',
            'routes'=> ['companies.payment','companies.payment.store'],
        ],
        [
            'label' => __('Provjeri podatke'),
            'icon'  => 'fi-user-check',
            'routes'=> ['companies.review'],
        ],
        [
            'label' => __('Hvala na prijavi'),
            'icon'  => 'fi-thumbs-up',
            'routes'=> ['companies.success'],
        ],
    ];

    // 2) Ako je došao explicitno $currentStep, koristi njega; inače odredi iz naziva rute
    $currentStep = $currentStep ?? (function () use ($steps) {
        foreach ($steps as $i => $s) {
            if (request()->routeIs(...$s['routes'])) {
                return $i + 1;
            }
        }
        return 1; // fallback
    })();
@endphp

        <!-- Steps (Navigation) -->
<div class="sticky-top pt-3" style="margin-top: -76px; background-color: #30536b;">
    <div class="d-block d-md-none" style="height: 62px"></div>
    <div class="d-none d-md-block d-lg-none" style="height: 70px"></div>
    <div class="d-none d-lg-block" style="height: 76px"></div>

    <div class="container pt-md-1">
        <div class="overflow-x-auto">
            <div class="d-flex flex-nowrap align-items-center gap-1 pb-3 mb-md-1">

                @foreach($steps as $i => $s)
                    @php
                        $index = $i + 1;
                        $state = $index < $currentStep ? 'done' : ($index === $currentStep ? 'active' : 'todo');
                        $circleClasses = match($state) {
                            'done','active' => 'bg-white',
                            default         => 'border border-white',
                        };
                        $iconClasses = $state === 'todo' ? 'text-white' : 'text-info';
                        $textClasses = 'fs-sm fw-semibold text-white '.($state === 'active' ? '' : 'opacity-75');
                        $hrOpacity   = $state === 'todo' ? 'opacity-25' : 'opacity-50';
                    @endphp

                    <div class="d-flex align-items-center gap-2 gap-sm-3 text-nowrap" @if($state==='active') aria-current="step" @endif>
                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 {{ $circleClasses }}" style="width: 44px; height: 44px">
                            <i class="{{ $s['icon'] }} fs-lg {{ $iconClasses }}"></i>
                        </div>
                        <div class="{{ $textClasses }}">{{ $s['label'] }}</div>
                    </div>

                    @if(!$loop->last)
                        <hr class="w-100 text-white {{ $hrOpacity }} my-0 mx-2">
                    @endif
                @endforeach

            </div>
        </div>
    </div>
</div>
