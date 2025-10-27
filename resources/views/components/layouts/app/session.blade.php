@if(session('success'))
    <!-- Success alert -->
    <div class="alert alert-success d-flex" role="alert">
        <div class="alert-icon">
            <i class="ci-check-circle"></i>
        </div>
        <div>{{ __('front/cart.uspjeh') }}..! {{ session('success') }}</div>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger d-flex" role="alert">
        <div class="alert-icon">
            <i class="ci-close-circle"></i>
        </div>
        <div>{{ __('front/cart.greska') }}..! {{ session('error') }}</div>
    </div>

@endif
@if(session('warning'))
    <div class="alert alert-warning d-flex" role="alert">
        <div class="alert-icon">
            <i class="ci-security-announcement"></i>
        </div>
        <div>Upozorenje..! {{ session('warning') }}</div>
    </div>
@endif

@if(session('status'))
    <div class="alert alert-success d-flex" role="alert">
        <div class="alert-icon">
            <i class="ci-check-circle"></i>
        </div>
        <div>Status..! {{ session('status') }}</div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
