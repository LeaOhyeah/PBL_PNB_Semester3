@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/slicknav.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/responsive.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        a:hover {
            color: black !important;
        }

        .remove-category-icon {
            color: #dc3545;
            /* Warna merah */
            font-size: 1rem;
        }

        .remove-category-icon:hover {
            color: #a71d2a;
            /* Warna merah gelap */
        }
    </style>

    <style>
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

        #map {
            height: 260px;
        }
    </style>
@endpush

@section('title')
    {{ env('APP_NAME') }} - Tentang Kami
@endsection

@section('content')
    <!-- About US Start -->
    <div class="about-area mt-30">
        <div class="container">

            <div class="row">
                <div class="col-lg-8">
                    <!-- Trending Tittle -->
                    <div class="about-right mb-90">
                        <div class="section-tittle mb-30 pt-30">
                            <h3>Tentang Kami</h3>
                        </div>
                        <div class="about-prea">

                            <p class="about-pera1 mb-25">Lorem ipsum dolor sit amet consectetur, adipisicing elit.
                                Consequatur ipsum placeat unde enim minus sequi, magnam dolore aut fugit quaerat hic natus
                                minima quos ipsa totam tempora, provident, incidunt laudantium praesentium ex. Dolorem
                                laborum at consequatur odio quidem veritatis consequuntur debitis perferendis ut aspernatur,
                                architecto laudantium consectetur amet tenetur doloremque, quos omnis. Quo, iure aliquid!
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="about-right mb-90">
                        <!-- Section Tittle -->
                        <div class="section-tittle mb-40 pt-30">
                            <h3>Ikuti Sosial Media Kami</h3>
                        </div>
                        <!-- Flow Socail -->
                        <div class="single-follow mb-45">
                            <div class="single-box">
                                <div class="follow-us d-flex align-items-center">
                                    <div class="follow-social">
                                        <a href="#"><img src="{{ asset('template/assets/img/news/icon-fb.png') }}"
                                                alt=""></a>
                                    </div>
                                    <div class="follow-count">
                                        <span>@berita</span>
                                    </div>
                                </div>
                                <div class="follow-us d-flex align-items-center">
                                    <div class="follow-social">
                                        <a href="#"><img src="{{ asset('template/assets/img/news/icon-tw.png') }}"
                                                alt=""></a>
                                    </div>
                                    <div class="follow-count">
                                        <span>@berita</span>
                                    </div>
                                </div>
                                <div class="follow-us d-flex align-items-center">
                                    <div class="follow-social">
                                        <a href="#"><img src="{{ asset('template/assets/img/news/icon-ins.png') }}"
                                                alt=""></a>
                                    </div>
                                    <div class="follow-count">
                                        <span>@berita</span>
                                    </div>
                                </div>
                                <div class="follow-us d-flex align-items-center">
                                    <div class="follow-social">
                                        <a href="#"><img src="{{ asset('template/assets/img/news/icon-yo.png') }}"
                                                alt=""></a>
                                    </div>
                                    <div class="follow-count">
                                        <span>@berita</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About US End -->

    <div class="contant-section mb-30">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="contact-title">Temukan Kami</h2>
                </div>
                <div class="col-lg-8">
                    <div id="map">

                    </div>
                    {{-- <form class="form-contact contact_form" action="contact_process.php" method="post" id="contactForm"
                        novalidate="novalidate">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <textarea class="form-control w-100" name="message" id="message" cols="30" rows="9"
                                        onfocus="this.placeholder = ''" onblur="this.placeholder = 'Tulis pesan'" placeholder=" Tulis pesan"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control valid" name="name" id="name" type="text"
                                        onfocus="this.placeholder = ''" onblur="this.placeholder = 'Masukkan nama anda'"
                                        placeholder="Masukkan nama anda">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control valid" name="email" id="email" type="email"
                                        onfocus="this.placeholder = ''"
                                        onblur="this.placeholder = 'Masukkan alamat email anda'"
                                        placeholder="Masukkan alamat email anda">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="genric-btn danger-border circle e-large">Kirim</button>
                        </div>
                    </form> --}}
                </div>
                <div class="col-lg-4">
                    <div class="media contact-info">
                        <span class="contact-info__icon"><i class="ti-home"></i></span>
                        <div class="media-body">
                            <h3>Bali, Indonesia.</h3>
                            <p>Denpasar, Denpasar Timur - 80238</p>
                        </div>
                    </div>
                    <div class="media contact-info">
                        <span class="contact-info__icon"><i class="ti-tablet"></i></span>
                        <div class="media-body">
                            <h3>+62 812 1234 1234</h3>
                            <p>Sen - Jum, 09:00 - 16:00 WITA</p>
                        </div>
                    </div>
                    <div class="media contact-info">
                        <span class="contact-info__icon"><i class="ti-email"></i></span>
                        <div class="media-body">
                            <h3>berita@example.com</h3>
                            <p>Kirimkan pertanyaan Anda kapan saja!!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- All JS Custom Plugins Link Here -->
    <script src="{{ asset('template/assets/js/vendor/modernizr-3.5.0.min.js') }}"></script>

    <!-- Jquery, Popper, Bootstrap -->
    <script src="{{ asset('template/assets/js/vendor/jquery-1.12.4.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/bootstrap.min.js') }}"></script>

    <!-- Jquery Mobile Menu -->
    <script src="{{ asset('template/assets/js/jquery.slicknav.min.js') }}"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="{{ asset('template/assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/slick.min.js') }}"></script>

    <!-- One Page, Animated-HeadLin -->
    <script src="{{ asset('template/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/animated.headline.js') }}"></script>

    <!-- Scrollup, nice-select, sticky -->
    <script src="{{ asset('template/assets/js/jquery.scrollUp.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/jquery.sticky.js') }}"></script>
    <script src="{{ asset('template/assets/js/jquery.magnific-popup.js') }}"></script>

    <!-- Contact JS -->
    <script src="{{ asset('template/assets/js/contact.js') }}"></script>
    <script src="{{ asset('template/assets/js/jquery.form.js') }}"></script>
    <script src="{{ asset('template/assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/mail-script.js') }}"></script>
    <script src="{{ asset('template/assets/js/jquery.ajaxchimp.min.js') }}"></script>

    <!-- Jquery Plugins, main Jquery -->
    <script src="{{ asset('template/assets/js/plugins.js') }}"></script>
    <script src="{{ asset('template/assets/js/main.js') }}"></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        var map = L.map('map').setView([-8.800995551547851, 115.1619892890814], 13);
        
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([-8.800995551547851, 115.1619892890814]).addTo(map);
    </script>
@endpush
