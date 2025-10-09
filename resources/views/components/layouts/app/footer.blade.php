<!-- Page footer -->
<!-- Page footer -->
<footer class="footer bg-body border-top pt-5" data-bs-theme="dark">
    <div class="container pt-sm-2 pt-md-3 pt-lg-4">
        <div class="accordion row pb-5 mb-sm-2 mb-md-3 mb-lg-4" id="footerLinks">

            <!-- Logo + Contacts -->
            <div class=" col-lg-3">
                <a class="d-inline-flex align-items-center text-dark-emphasis text-decoration-none mb-4" href="index.html">
              <span class="flex-shrink-0 text-primary rtl-flip me-2">
                <img src="{{ asset('theme1/assets/companythrone-round.svg') }}" alt="Companythrone">
              </span>
                    <span class="fs-4 fw-semibold">{{ config('app.name','Companythrone') }}</span>
                </a>

            </div>

            <div class=" col-lg-3">
                <h6 class="accordion-header" >
                    <span class="h5 d-none d-sm-block">Kontakt</span>
                    <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#quickLinks" aria-expanded="false" aria-controls="quickLinks">Quick links</button>
                </h6>
                <ul class="list-unstyled gap-3">
                    <li>
                        <div class="position-relative d-flex align-items-center">
                            <i class="fi-mail fs-lg text-body me-2"></i>
                            <a class="text-dark-emphasis text-decoration-none hover-effect-underline stretched-link" href="mailto:contact@example.com">info@companythrone.hr</a>
                        </div>
                    </li>
                    <li>
                        <div class="position-relative d-flex align-items-center">
                            <i class="fi-phone-call fs-lg text-body me-2"></i>
                            <a class="text-dark-emphasis text-decoration-none hover-effect-underline stretched-link" href="tel:+38512383733">+385 1 23 83 733</a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Columns with links that are turned into accordion on screens < 500px wide (sm breakpoint) -->
            <div class="accordion-item  col-lg-3 border-0">
                <h6 class="accordion-header" id="quickLinksHeading">
                    <span class="h5 d-none d-sm-block">Uvjeti korištenja</span>
                    <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#quickLinks" aria-expanded="false" aria-controls="quickLinks">Quick links</button>
                </h6>
                <div class="accordion-collapse collapse d-sm-block" id="quickLinks" aria-labelledby="quickLinksHeading" data-bs-parent="#footerLinks">
                    <ul class="nav flex-column gap-2 pt-sm-1 pt-lg-2 pb-3 pb-sm-0 mt-n1 mb-1 mb-sm-0">
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="{{ nav_url('pages', id: 3) }}">Opći uvjeti korištenja</a>
                        </li>
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="{{ nav_url('pages', id: 2) }}">Politika privatnosti</a>
                        </li>
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="{{ nav_url('pages', id: 1) }}">Pravila o kolačićima</a>
                        </li>


                    </ul>
                </div>
                <hr class="d-sm-none my-0">
            </div>
            <div class="accordion-item  col-lg-3  border-0">
                <h6 class="accordion-header" id="profileLinksHeading">
                    <span class="h5 d-none d-sm-block">Informacije</span>
                    <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#profileLinks" aria-expanded="false" aria-controls="profileLinks">Profile</button>
                </h6>
                <div class="accordion-collapse collapse d-sm-block" id="profileLinks" aria-labelledby="profileLinksHeading" data-bs-parent="#footerLinks">
                    <ul class="nav flex-column gap-2 pt-sm-1 pt-lg-2 pb-3 pb-sm-0 mt-n1 mb-1 mb-sm-0">
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="{{ nav_url('pages', id: 4) }}">O nama</a>
                        </li>
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="{{ localized_route('faq') }}">Česta pitanja</a>
                        </li>
                        <li class="pt-1">
                            <a class="nav-link hover-effect-underline d-inline text-body fw-normal p-0" href="{{ localized_route('kontakt') }}">Kontakt</a>
                        </li>

                    </ul>
                </div>
                <hr class="d-sm-none my-0">
            </div>


        </div>

        <!-- Contact link + Social links + Copyright -->
        <div class="border-top pt-2 pb-md-2">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 text-center text-lg-start mb-3 mb-lg-0">
                    <p class="text-body-secondary fs-sm text-center text-lg-start mb-0">&copy; Sva prava pridržana. Companythrone </p>

                </div>
                <div class="col-lg-6  d-lg-flex justify-content-end">
                    <p class="text-body-secondary fs-sm text-center text-lg-end mb-0"> Web by <a class="text-body fw-medium text-decoration-none hover-effect-underline" href="https://www.agmedia.hr/" target="_blank" >AG media</a></p>
                </div>
            </div>
        </div>
    </div>
</footer>

