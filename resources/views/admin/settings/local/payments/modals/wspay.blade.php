{{-- WSPay --}}
<div class="modal fade" id="payment-modal-wspay" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ __('back/shop/payments/wspay.title') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-10 mx-auto">

                        {{-- Title + language pills (identical to Bank/COD) --}}
                        @include('admin.settings.partials.lang-title', ['code'  => 'wspay', 'label' => __('back/shop/payments/wspay.input_title')])

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('back/shop/payments/wspay.min_order_amount') }}</label>
                                <input type="text" class="form-control" data-config="min">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ __('back/shop/payments/wspay.geo_zone') }}
                                    <span class="small text-muted">{{ __('back/shop/payments/wspay.geo_zone_label') }}</span>
                                </label>
                                <select class="form-control" name="geo_zone">
                                    <option value="">{{ __('back/shop/payments/wspay.select_geo') }}</option>
                                    @foreach($geozones as $gz)
                                        <option value="{{ $gz->id }}">
                                            {{ isset($gz->title->{current_locale()}) ? $gz->title->{current_locale()} : ($gz->title->en ?? $gz->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Short description (localized) --}}
                        @include('admin.settings.partials.lang-description', ['code'  => 'wspay', 'label' => __('back/shop/payments/wspay.short_desc')])

                        {{-- Provider config --}}
                        <div class="border rounded p-3 mt-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">ShopID</label>
                                    <input type="text" class="form-control" data-config="shop_id">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SecretKey</label>
                                    <input type="text" class="form-control" data-config="secret_key">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Callback URL</label>
                                    <input type="text" class="form-control" data-config="callback" data-default="{{ url('/') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Test Mode</label>
                                    <select class="form-control" data-config="test" data-default="1">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('back/shop/payments/wspay.sort_order') }}</label>
                                <input type="text" class="form-control" name="sort_order" value="0">
                            </div>
                            <div class="col-md-6 d-flex align-items-end justify-content-between">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status">
                                    <label class="form-check-label">{{ __('back/shop/payments/wspay.status_title') }}</label>
                                </div>
                                <input type="hidden" name="id" value="0">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/payments/wspay.cancel') }}</button>
                <button class="btn btn-primary" onclick="savePayment('wspay');">{{ __('back/shop/payments/wspay.save') }}</button>
            </div>
        </div>
    </div>
</div>
