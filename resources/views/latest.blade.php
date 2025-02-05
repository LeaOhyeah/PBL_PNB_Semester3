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
    {{ env('APP_NAME') }} - Terbaru
@endsection

@section('content')


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


    <!-- Berita terbaru berdasarkan semua kategori-->
    <div class="weekly2-news-area pt-10">
        <div class="container">
            <div class="weekly2-wrapper">
                <!-- section Tittle -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-tittle mb-30">
                            <div class="selected-category mt-3">
                                    <h3>
                                        Terbaru Dari Semua Kategori
                                    </h3>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="weekly2-news-active d-flex dot-style">
                            @php
                                $repeatTimes = max(0, 5 - $latest_news->count()); // Hitung berapa kali harus mengulang
                            @endphp

                            <!-- Tampilkan data asli -->
                            @foreach ($latest_news as $news)
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
                                    $news = $latest_news[$i % $latest_news->count()]; // Ambil data secara berulang
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
    <!-- End Berita terbaru berdasarkan semua kategori -->


    @foreach ($latest_by_categories as $index => $category)
         <!-- Berita terbaru berdasarkan semua kategori-->
    <div class="weekly2-news-area pt-10">
        <div class="container">
            <div class="weekly2-wrapper">
                <!-- section Tittle -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-tittle mb-30">
                            <div class="selected-category mt-3">
                                    <h3>
                                        Terbaru Dari Kategori {{$category->name}}
                                    </h3>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="weekly2-news-active d-flex dot-style">
                            @php
                                $repeatTimes = max(0, 5 - $category->news->count()); // Hitung berapa kali harus mengulang
                            @endphp

                            <!-- Tampilkan data asli -->
                            @foreach ($category->news as $news)
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
                                    $newsAdd = $category->news[$i % $category->news->count()]; // Ambil data secara berulang
                                @endphp
                                <div class="weekly2-single">
                                    <div class="weekly2-img">
                                        <img src="https://img.youtube.com/vi/{{ $newsAdd->content_url }}/hqdefault.jpg"
                                            class="img-fluid" alt="Thumbnail">
                                    </div>
                                    <div class="weekly2-caption">
                                        <a href="{{ route('page.filter', ['user' => $newsAdd->user_id]) }}">
                                            <span class="color1">{{ $newsAdd->user->name }}</span>
                                        </a>
                                        <h4><a
                                                href="{{ route('news.show', $newsAdd->id) }}">{{ \Illuminate\Support\Str::limit($news->title, 40) }}</a>
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
    <!-- End Berita terbaru berdasarkan semua kategori -->
    @endforeach

    <br>
    <br>
    <br>
    <br>
    <br>
    
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
