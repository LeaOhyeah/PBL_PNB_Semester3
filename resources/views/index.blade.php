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
        a:hover{
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
    </style>
@endpush

@section('title')
    {{ env('APP_NAME') }} - Beranda
@endsection

@section('content')
    @php
        use Illuminate\Support\Facades\Cookie;
        $userCategoryId = Cookie::get('userCategorySelected');
    @endphp

    <!-- Form Pencarian -->
    <div class="container mt-4" style="max-width: 1100px">
        <form action="{{ route('page.filter') }}" method="GET">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Cari berita..." required>
                <div class="input-group-append">
                    <button class="genric-btn danger ml-3 radius" type="submit">Cari</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal untuk memilih kategori -->
    <div class="modal" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">
                        <span id="categoryModalTitle">Bantu Kami Menampilkan Berita yang Sesuai dengan Anda</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section-tittle mb-30">
                                <h4 id="categoryHeading">Katagori Apa yang Anda Sukai</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($categories as $category)
                            <div class="m-1">
                                <button class="genric-btn danger-border circle category-btn w-100"
                                    data-category-id="{{ $category->id }}">
                                    {{ $category->name }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="genric-btn primary radius" id="skipCategoryBtn" data-dismiss="modal">
                        Lewati
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Berita terbaru -->
    <div class="trending-area fix">
        <div class="container">
            <div class="trending-main">

                <!-- Tittle -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="trending-tittle">
                            <strong class="bg-danger">Berita Terbaru</strong>
                            <p class="ml-3">Empat Berita Terbaru dan Terpanas Untuk Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 mb-50">

                        <!-- Top -->
                        <div class=" mb-40">
                            <div class="trend-top-img">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item"
                                        src="https://www.youtube.com/embed/{{ $hero_latest->content_url }}"
                                        allowfullscreen></iframe>
                                </div>
                                <div class="mt-15">
                                    <h2><a
                                            href="{{ route('news.show', $hero_latest->id) }}">{{ \Illuminate\Support\Str::limit($hero_latest->title, 60) }}</a>
                                    </h2>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom -->
                        <div class="trending-bottom mb-30">
                            <div class="row">
                                @foreach ($latest_news as $news)
                                    <div class="col-lg-4">
                                        <div class="single-bottom mb-35">
                                            <div class="trend-bottom-img mb-30">
                                                <img src="https://img.youtube.com/vi/{{ $news->content_url }}/hqdefault.jpg"
                                                    class="img-fluid" alt="Thumbnail">
                                            </div>
                                            <div class="trend-bottom-cap">
                                                <a href="{{ route('page.filter', ['user' => $news->user_id]) }}">
                                                    <span class="font-weight-bold">{{ $news->user->name }}</span>
                                                </a>
                                                <h4><a
                                                        href="{{ route('news.show', $hero_latest->id) }}">{{ \Illuminate\Support\Str::limit($news->title, 48) }}</a>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Riht content -->
                    <div class="col-lg-4">
                        @foreach ($hero_categories as $category)
                            @if ($category->news->isNotEmpty())
                                <div class="trand-right-single d-flex">
                                    <div class="trand-right-img">
                                        <img src="https://img.youtube.com/vi/{{ $category->news->first()->content_url }}/hqdefault.jpg"
                                            class="img-fluid" style="max-width: 120px; height: auto;" alt="Thumbnail">
                                    </div>
                                    <div class="trand-right-cap">
                                        <a href="{{ route('page.filter', ['category' => $category->slug]) }}">
                                            <span class="color4">{{ $category->name }}</span>
                                        </a>
                                        <h4><a href="{{ route('news.show', $category->news->first()->id) }}">
                                                {{ \Illuminate\Support\Str::limit($category->news->first()->title, 40) }}
                                            </a></h4>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- End Berita terbaru -->

    <!-- Berita berdasarkan kategori dipilih-->
    <div class="weekly2-news-area pt-10">
        <div class="container">
            <div class="weekly2-wrapper">
                <!-- section Tittle -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-tittle mb-30">
                            <div class="selected-category mt-3">
                                @if ($userCategoryId != 'skip')
                                    <h3>
                                        Terbaru dari Kategori {{ $newsFromCategory->first()->category->name }}
                                        <i class="fa fa-times-circle remove-category-icon"
                                            style="cursor: pointer; margin-left: 5px;"
                                            title="Berhenti tampilkan kategori ini"></i>
                                    </h3>
                                @else
                                    <h3>
                                        Mungkin Cocok dengan Anda
                                    </h3>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="weekly2-news-active d-flex dot-style">
                            @php
                                $repeatTimes = max(0, 5 - $newsFromCategory->count()); // Hitung berapa kali harus mengulang
                            @endphp

                            <!-- Tampilkan data asli -->
                            @foreach ($newsFromCategory as $news)
                                <div class="weekly2-single">
                                    <div class="weekly2-img">
                                        <img src="https://img.youtube.com/vi/{{ $news->content_url }}/hqdefault.jpg"
                                            class="img-fluid" alt="Thumbnail">
                                    </div>
                                    <div class="weekly2-caption">
                                        <a href="{{ route('page.filter', ['user' => $news->user_id]) }}">
                                            <span class="color1">{{ $news->user->name }}</span>
                                        </a>
                                        <h4><a
                                                href="{{ route('news.show', $news->id) }}">{{ \Illuminate\Support\Str::limit($news->title, 40) }}</a>
                                        </h4>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Ulang data yang ada hingga mencapai 5 -->
                            @for ($i = 0; $i < $repeatTimes; $i++)
                                @php
                                    $news = $newsFromCategory[$i % $newsFromCategory->count()]; // Ambil data secara berulang
                                @endphp
                                <div class="weekly2-single">
                                    <div class="weekly2-img">
                                        <img src="https://img.youtube.com/vi/{{ $news->content_url }}/hqdefault.jpg"
                                            class="img-fluid" alt="Thumbnail">
                                    </div>
                                    <div class="weekly2-caption">
                                        <a href="{{ route('page.filter', ['user' => $news->user_id]) }}">
                                            <span class="color1">{{ $news->user->name }}</span>
                                        </a>
                                        <h4><a
                                                href="{{ route('news.show', $news->id) }}">{{ \Illuminate\Support\Str::limit($news->title, 40) }}</a>
                                        </h4>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Berita berdasarkan kategori dipilih -->

    <!-- Berita berdasarkan semua kategori -->
    <section class="whats-news-area pt-50 pb-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row d-flex justify-content-between">
                        <div class="col-lg-3 col-md-3">
                            <div class="section-tittle mb-30">
                                <h3>Berdasarkan Kategori</h3>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="properties__button">
                                <!-- Nav Button -->
                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        @foreach ($by_categories as $index => $category)
                                            <a class="nav-item nav-link {{ $index === 0 ? 'active' : '' }}"
                                                id="nav-{{ $category->slug }}-tab" data-toggle="tab" role="tab"
                                                aria-controls="nav-{{ $category->slug }}"
                                                href="#nav-{{ $category->slug }}">
                                                {{ $category->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </nav>
                                <!-- End Nav Button -->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <!-- Nav Card -->
                            <div class="tab-content" id="nav-tabContent">
                                @foreach ($by_categories as $index => $category)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                        id="nav-{{ $category->slug }}" role="tabpanel"
                                        aria-labelledby="nav-{{ $category->slug }}-tab">
                                        <div class="trending-bottom mb-30">
                                            <div class="row">
                                                @foreach ($category->news as $news)
                                                    <div class="col-lg-3 col-md-6">
                                                        <div class="single-bottom mb-35">
                                                            <div class="trend-bottom-img mb-30">
                                                                <img src="https://img.youtube.com/vi/{{ $news->content_url }}/hqdefault.jpg"
                                                                    class="img-fluid" alt="Thumbnail">
                                                            </div>
                                                            <div class="trend-bottom-cap">
                                                                <a
                                                                    href="{{ route('page.filter', ['user' => $news->user_id]) }}">
                                                                    <span
                                                                        class="font-weight-bold text-dark">{{ $news->user->name }}</span>
                                                                </a>
                                                                <h5>
                                                                    <a href="{{ route('news.show', $news->id) }}">
                                                                        {{ \Illuminate\Support\Str::limit($news->title, 40) }}
                                                                    </a>
                                                                </h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!-- End Nav Card -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Berita berdasarkan semua kategori-->
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

    <!-- Jquery Plugins, main Jquery -->
    <script src="{{ asset('template/./assets/js/plugins.js') }}"></script>
    <script src="{{ asset('template/./assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>

    <script>
        $(document).ready(function() {
            const currentCategory = Cookies.get('userCategorySelected');

            // Tampilkan modal
            if (!currentCategory) {
                $('#categoryModal').modal('show');
            }

            // Simpan kategori
            $('.category-btn').on('click', function() {
                const categoryId = $(this).data('category-id');
                if (categoryId) {
                    Cookies.set('userCategorySelected', categoryId, {
                        expires: 7,
                        path: '/',
                        sameSite: 'lax'
                    });
                    // Tutup modal dan reload
                    $('#categoryModal').modal('hide');
                    location.reload();
                }
            });

            // Tombol Lewati
            $('#skipCategoryBtn').on('click', function() {
                Cookies.set('userCategorySelected', 'skip', {
                    expires: 7,
                    path: '/',
                    sameSite: 'lax'
                });
                $('#categoryModal').modal('hide');
                location.reload();
            });

            // Menghapus kategori 
            $(document).on('click', '.remove-category-icon', function() {
                Cookies.remove('userCategorySelected', {
                    path: '/'
                });
                location.reload();
            });
        });
    </script>
@endpush
