@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/ticker-style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/slicknav.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">

    <style>
        a:hover {
            color: black !important;
        }

        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        @media (min-width: 576px) {
            .container {
                max-width: 540px;
            }
        }

        @media (min-width: 768px) {
            .container {
                max-width: 720px;
            }
        }

        @media (min-width: 992px) {
            .container {
                max-width: 960px;
            }
        }

        @media (min-width: 1200px) {
            .container {
                max-width: 90%;
            }
        }
    </style>
@endpush

@section('title')
    {{ env('APP_NAME') }} - {{ $title }}
@endsection

@section('content')
    <div class="weekly2-news-area pt-10">
        <div class="container">
            <div class="weekly2-wrapper">
                <!-- section Tittle -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-tittle mb-30">
                            <h3>{{ $title }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Grid Berita -->
                <div class="row">
                    @foreach ($news as $item)
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <div class="weekly2-single">
                                <div class="weekly2-img">
                                    <img src="https://img.youtube.com/vi/{{ $item->content_url }}/hqdefault.jpg"
                                        class="img-fluid w-100" alt="Thumbnail">
                                </div>
                                <div class="weekly2-caption">
                                    @php echo $infoText($item) @endphp
                                    <h4>
                                        <a href="{{ route('news.show', $item->id) }}">
                                            {{ Str::limit($item->title, 50) }}
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-area pb-45 text-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="single-wrap d-flex justify-content-center">
                                    {{ $news->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- All JS Custom Plugins Link Here here -->
    <script src="{{ asset('template/./assets/js/vendor/modernizr-3.5.0.min.js') }}"></script>

    <!-- Jquery, Popper, Bootstrap -->
    <script src="{{ asset('template/./assets/js/vendor/jquery-1.12.4.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/bootstrap.min.js') }}"></script>

    <!-- Jquery Mobile Menu -->
    <script src="{{ asset('template/./assets/js/jquery.slicknav.min.js') }}"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="{{ asset('template/./assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/slick.min.js') }}"></script>

    <!-- One Page, Animated-HeadLin -->
    <script src="{{ asset('template/./assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/animated.headline.js') }}"></script>
    <script src="{{ asset('template/./assets/js/jquery.magnific-popup.js') }}"></script>

    <!-- Breaking New Pluging -->
    <script src="{{ asset('template/./assets/js/jquery.ticker.js') }}"></script>
    <script src="{{ asset('template/./assets/js/site.js') }}"></script>

    <!-- Scrollup, nice-select, sticky -->
    <script src="{{ asset('template/./assets/js/jquery.scrollUp.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/jquery.sticky.js') }}"></script>

    <!-- contact js -->
    <script src="{{ asset('template/./assets/js/contact.js') }}"></script>
    <script src="{{ asset('template/./assets/js/jquery.form.js') }}"></script>
    <script src="{{ asset('template/./assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/mail-script.js') }}"></script>
    <script src="{{ asset('template/./assets/js/jquery.ajaxchimp.min.js') }}"></script>

    <!-- Jquery Plugins, main Jquery -->
    <script src="{{ asset('template/./assets/js/plugins.js') }}"></script>
    <script src="{{ asset('template/./assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
@endpush
