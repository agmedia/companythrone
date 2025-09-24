@extends('layouts.app')
@section('title', __('home.title'))
@section('content')

    <div class="container py-5 mt-n3 mt-sm-0 my-xxl-3">

        <h1 class="h2 pb-2 pb-lg-3">Česta pitanja</h1>
        <!-- Accordion with alternative button icon -->
        <div class="accordion accordion-alt-icon" id="accordionExample">

            <!-- Item (expanded) -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="headingOne">
                    <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <span class="hover-effect-underline stretched-link me-2">Accordion Item #1</span>
                    </button>
                </h3>
                <div class="accordion-collapse collapse show" id="collapseOne" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">This is the first item's accordion body. It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element.</div>
                </div>
            </div>

            <!-- Item -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="headingTwo">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <span class="hover-effect-underline stretched-link me-2">Accordion Item #2</span>
                    </button>
                </h3>
                <div class="accordion-collapse collapse" id="collapseTwo" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div class="accordion-body">This is the second item's accordion body. It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element.</div>
                </div>
            </div>

            <!-- Item -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="headingThree">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <span class="hover-effect-underline stretched-link me-2">Accordion Item #3</span>
                    </button>
                </h3>
                <div class="accordion-collapse collapse" id="collapseThree" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                    <div class="accordion-body">This is the third item's accordion body. It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element.</div>
                </div>
            </div>
        </div>



    </div>

@endsection
