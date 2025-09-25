<div class="modal fade" id="payment-modal-cod" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">{{ __('back/shop/payments/cod.title') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        @include('admin.settings.partials.lang-title', ['code'  => 'cod', 'label' => __('back/shop/payments/cod.title')])

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('back/shop/payments.sort_order') }}</label>
                                <input type="text" class="form-control" name="sort_order" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">{{ __('back/shop/payments.status') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="status" checked>
                                    <label class="form-check-label">{{ __('back/common.status.active') }}</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        @foreach($locales as $lc => $ln)
                            <div class="mb-3">
                                <label class="form-label">{{ __('back/shop/payments/cod.instructions') }} ({{ strtoupper($lc) }})</label>
                                <textarea class="form-control" rows="3" data-config="instructions.{{ $lc }}" data-default=""></textarea>
                            </div>
                        @endforeach

                        <input type="hidden" name="id" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/payments.cancel') }}</button>
                <button class="btn btn-primary" onclick="savePayment('cod');">{{ __('back/shop/payments.save') }}</button>
            </div>
        </div>
    </div>
</div>
