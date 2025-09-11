<!-- [ Footer ] start -->
<footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
        <div class="row">
            <div class="col my-1">
                <p class="m-0">© {{ date('Y') }} {{ config('shop.name') }}</p>
            </div>
            <div class="col-auto my-1">
                <ul class="list-inline footer-link mb-0">
                    <li class="list-inline-item"><a href="{{ route('dashboard') }}">@lang('back/nav.dashboard')</a></li>
                    <li class="list-inline-item"><a href="{{ route('users.profile') }}">@lang('back/common.roles.administrator')</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- [ Footer ] end -->


