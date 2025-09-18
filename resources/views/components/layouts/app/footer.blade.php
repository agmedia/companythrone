<!-- Page footer -->
<!-- Page footer -->
<footer class="footer bg-body border-top pt-5" data-bs-theme="dark">
    <div class="container pt-sm-2 pt-md-3 pt-lg-4">
        <div class="accordion row pb-5 mb-sm-2 mb-md-3 mb-lg-4" id="footerLinks">

            <!-- Logo + Contacts -->
            <div class=" col-lg-6">
                <a class="d-inline-flex align-items-center text-dark-emphasis text-decoration-none mb-4" href="index.html">
              <span class="flex-shrink-0 text-primary rtl-flip me-2">
                <img src="{{ asset('theme1/assets/companythrone-round.svg') }}" alt="Companythrone">
              </span>
                    <span class="fs-4 fw-semibold">    {{ config('app.name','Companythrone') }}</span>
                </a>
                <ul class="list-unstyled gap-3">
                    <li>
                        <div class="position-relative d-flex align-items-center">
                            <i class="fi-mail fs-lg text-body me-2"></i>
                            <a class="text-dark-emphasis text-decoration-none hover-effect-underline stretched-link" href="mailto:contact@example.com">contact@example.com</a>
                        </div>
                    </li>
                    <li>
                        <div class="position-relative d-flex align-items-center">
                            <i class="fi-phone-call fs-lg text-body me-2"></i>
                            <a class="text-dark-emphasis text-decoration-none hover-effect-underline stretched-link" href="tel:+15053753082">+1&nbsp;50&nbsp;537&nbsp;53&nbsp;082</a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Columns with links that are turned into accordion on screens < 500px wide (sm breakpoint) -->
            <div class="accordion-item  col-lg-3 border-0">
                <h6 class="accordion-header" id="quickLinksHeading">
                    <span class="h5 d-none d-sm-block">Quick links</span>
                    <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#quickLinks" aria-expanded="false" aria-controls="quickLinks">Quick links</button>
                </h6>
                <div class="accordion-collapse collapse d-sm-block" id="quickLinks" aria-labelledby="quickLinksHeading" data-bs-parent="#footerLinks">
                    <ul class="nav flex-column gap-2 pt-sm-1 pt-lg-2 pb-3 pb-sm-0 mt-n1 mb-1 mb-sm-0">
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="#!">Top cities</a>
                        </li>
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="#!">Accommodation</a>
                        </li>
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="#!">Cafes &amp; restaurants</a>
                        </li>


                    </ul>
                </div>
                <hr class="d-sm-none my-0">
            </div>
            <div class="accordion-item  col-lg-3  border-0">
                <h6 class="accordion-header" id="profileLinksHeading">
                    <span class="h5 d-none d-sm-block">Profile</span>
                    <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#profileLinks" aria-expanded="false" aria-controls="profileLinks">Profile</button>
                </h6>
                <div class="accordion-collapse collapse d-sm-block" id="profileLinks" aria-labelledby="profileLinksHeading" data-bs-parent="#footerLinks">
                    <ul class="nav flex-column gap-2 pt-sm-1 pt-lg-2 pb-3 pb-sm-0 mt-n1 mb-1 mb-sm-0">
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="#!">My account</a>
                        </li>
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="#!">My listings</a>
                        </li>
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="#!">Gift cards</a>
                        </li>

                    </ul>
                </div>
                <hr class="d-sm-none my-0">
            </div>


        </div>

        <!-- Contact link + Social links + Copyright -->
        <div class="border-top pt-2 pb-md-2">
            <div class="row align-items-center py-4">
                <div class="col-lg-4 text-center text-lg-start mb-3 mb-lg-0">
                    <div class="h5 d-none d-sm-block mb-0">
                        <span class="text-body-secondary fw-normal me-3">Trebate pomoć?</span>
                        <a class="text-white text-decoration-none hover-effect-underline" href="#!">Kontaktirajte nas</a>
                    </div>
                    <div class="h6 d-sm-none mb-0">
                        <span class="text-body-secondary fw-normal me-2">Trebate pomoć??</span>
                        <a class="text-white text-decoration-none hover-effect-underline" href="#!">Kontaktirajte nas</a>
                    </div>
                </div>
                <div class="col-lg-3 col-xl-4 d-flex justify-content-center mb-3 mb-lg-0">
                    <a class="btn btn-icon fs-base btn-outline-secondary border-0" href="#!" data-bs-toggle="tooltip" data-bs-template='<div class="tooltip fs-xs mb-n2" role="tooltip"><div class="tooltip-inner bg-transparent text-white opacity-75 p-0"></div></div>' title="Instagram" aria-label="Follow us on Instagram">
                        <i class="fi-instagram"></i>
                    </a>
                    <a class="btn btn-icon fs-base btn-outline-secondary border-0" href="#!" data-bs-toggle="tooltip" data-bs-template='<div class="tooltip fs-xs mb-n2" role="tooltip"><div class="tooltip-inner bg-transparent text-white opacity-75 p-0"></div></div>' title="Facebook" aria-label="Follow us on Facebook">
                        <i class="fi-facebook"></i>
                    </a>
                    <a class="btn btn-icon fs-base btn-outline-secondary border-0" href="#!" data-bs-toggle="tooltip" data-bs-template='<div class="tooltip fs-xs mb-n2" role="tooltip"><div class="tooltip-inner bg-transparent text-white opacity-75 p-0"></div></div>' title="X (Twitter)" aria-label="Follow us on X (Twitter)">
                        <i class="fi-x"></i>
                    </a>
                </div>
                <div class="col-lg-5 col-xl-4 d-lg-flex justify-content-end">
                    <p class="text-body-secondary fs-sm text-center text-lg-start mb-0">&copy; Sva prava pridržana. Web by <a class="text-body fw-medium text-decoration-none hover-effect-underline" href="https://www.agmedia.hr/" target="_blank" >Ag media</a></p>
                </div>
            </div>
        </div>
    </div>
</footer>

