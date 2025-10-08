<div class="alert alert-secondary">
    {{ __('back/shop/payments/bank.front_help') }}
</div>

<form name="pay" class="needs-validation w-100" action="{{ $data['callback'] }}" novalidate method="GET">
    @csrf
    <input type="hidden" name="provjera" value="{{ $data['id'] }}">
    <input type="hidden" name="code" value="bank">

    {{--<div class="d-block pt-0 pb-2 text-start">
        <div class="alert alert-info d-flex" role="alert">
            <div class="alert-icon">
                <i class="ci-announcement"></i>
            </div>
            <div><small>{{ __('front/cart.prihvati') }}</small></div>
        </div>
    </div>--}}

    <div class="form-check form-check-inline">
        <label class="form-check-label" for="ex-check-4">{{ __('Slažem se sa') }} {!! __(' :terms_of_service', [
                                                'terms_of_service' => '<a data-bs-toggle="modal" data-bs-target="#exampleModal" class="link-fx">'.__('Uvijetima kupovine').'</a>',
                                                'privacy_policy' => '<a target="_blank" href="'.route('home').'" class="link-fx">'.__('Privacy Policy').'</a>',
                                        ]) !!}</label>
        <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
        <div class="invalid-feedback" id="terms">{{ __('front/cart.morate_se_sloziti') }}</div>
    </div>

    <div class="d-flex mt-3">
        <div class="w-50 pe-3">
            <a class="btn btn-outline-primary d-block w-100" href="{{ route('companies.payment') }}"><i class="ci-arrow-left  me-1"></i><span class="d-none d-sm-inline">{{ __('Povratak na plaćanje') }}</span><span class="d-inline d-sm-none">{{ __('Povratak') }}</span></a>
        </div>
        <div class="w-50 ps-2">
            <button class="btn btn-primary w-100" type="submit"><span>{{ __('Dovrši plaćanje') }}</span><i class="ci-arrow-right  ms-1"></i></button>
        </div>

    </div>

</form>




