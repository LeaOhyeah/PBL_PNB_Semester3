<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }} - Home</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    {{-- icon tab belum ada --}}
    {{--
    <link href="{{ asset('template/img/favicon.ico') }}" rel="icon"> --}}

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('template/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('template/css/style.css') }}" rel="stylesheet">

    <style>
        body {
            /* background-color: #2c2c2c60; */
            /* color: rgb(0, 183, 255) */
        }

        .responsive-iframe {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
        }

        .responsive-iframe iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .carousel-blur-overlay {
            position: relative;
        }

        .carousel-blur-right {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 50px;
            pointer-events: none;
            z-index: 10;
        }

        .carousel-blur-right {
            right: 0;
            background: linear-gradient(to left, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
        }

        @media (max-width: 576px) {
            .carousel-blur-right {
                width: 20px;
                background: linear-gradient(to left, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0));
            }
        }

        @media (max-width: 768px) {
            .owl-nav {
                display: none !important;
                /* Sembunyikan tombol navigasi */
            }
        }

        .pagination .page-link {
            font-weight: bold;
            /* Membuat nomor tebal */
            font-size: 1.1rem;
            /* (Opsional) Perbesar teks jika diperlukan */
        }

        .pagination .page-link:hover {
            font-weight: bold;
            /* Tetap tebal saat hover */
        }

        .pagination .page-item.active .page-link {
            font-weight: bolder;
            /* Lebih tebal untuk item aktif */
        }
    </style>
</head>

<body>

    <!-- Navbar Start -->
    <div class="container-fluid p-0 mb-5">
        <nav class="navbar fixed-top navbar-expand-lg bg-dark navbar-dark py-2 py-lg-0 px-lg-5">
            <a href="index.html" class="navbar-brand d-none d-lg-block">
                <h1 class="m-0 display-5 mr-5 text-uppercase text-primary">
                    {{ config('app.name') }}
                </h1>
            </a>
            <!-- small display / mobile -->
            <a href="index.html" class="navbar-brand d-block d-lg-none">
                <h1 class="m-0 display-4 mr-5 text-uppercase text-primary">
                    {{ config('app.name') }}
                </h1>
            </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between px-0 px-lg-3" id="navbarCollapse">
                <div class="navbar-nav mr-auto py-0">
                    <a href="index.html" class="nav-item nav-link active">Beranda</a>
                    <a href="category.html" class="nav-item nav-link">Kategori</a>
                    <a href="contact.html" class="nav-item nav-link">Riwayat</a>
                    <a href="single.html" class="nav-item nav-link">Single News</a>
                    {{-- <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Dropdown</a>
                        <div class="dropdown-menu rounded-0 m-0">
                            <a href="#" class="dropdown-item">Menu item 1</a>
                            <a href="#" class="dropdown-item">Menu item 2</a>
                            <a href="#" class="dropdown-item">Menu item 3</a>
                        </div>
                    </div> --}}
                </div>
                <div class="input-group ml-auto d-none d-lg-flex" style="width: 100%; max-width: 300px;">
                    <input type="text" class="form-control border-0" placeholder="Keyword">
                    <div class="input-group-append">
                        <button class="input-group-text bg-primary text-dark border-0 px-3"><i
                                class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- Navbar End -->


    <!-- Untuk anda with Carousel Start -->
    <div class="container-fluid py-4">
        <div class="">
            <div class="col-lg-12">
                <div class="section-title">
                    <h4 class="m-0 text-uppercase font-weight-bold">Untuk Anda</h4>
                    <a class="text-secondary font-weight-medium text-decoration-none" href="">Lebih banyak..</a>
                </div>
                <div class="carousel-blur-overlay">
                    <div class="carousel-blur-right"></div>
                    <div class="owl-carousel news-carousel carousel-item-5 position-relative">
                        @for ($i = 0; $i < 3; $i++)
                            <div class="position-relative mb-3">
                                <img class="img-fluid rounded" src="https://img.youtube.com/vi/k-J9BVBjK3o/sddefault.jpg"
                                    style="object-fit: cover">
                                <div class="bg-white border border-top-0 p-4">
                                    <div class="mb-2">
                                        <a class="badge badge-primary text-uppercase font-weight-semi-bold p-2 mr-2"
                                            href="">Category</a>
                                        <a class="text-body" href=""><small>Jan 01, 2045</small></a>
                                    </div>
                                    <a class="h5 d-none d-lg-block mb-0 text-secondary text-uppercase font-weight-bold"
                                        href="">Lorem ipsum dolor sit amet elit...</a>
                                    <a class="small d-block d-lg-none mb-0 text-secondary text-uppercase font-weight-bold"
                                        href="">Lorem ipsum dolor sit amet elit...</a>
                                </div>
                                <div class="d-flex justify-content-between bg-white border border-top-0 p-4">
                                    <div class="d-flex align-items-center">

                                        <small>John Doe</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <small class="ml-3"><i class="far fa-eye mr-2"></i>12345</small>
                                        <small class="ml-3"><i class="far fa-comment mr-2"></i>123</small>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Untuk anda with Carousel End -->

    <!-- Sedang panas with Carousel Start -->
    <div class="container-fluid">
        <div class="">
            <div class="col-lg-12">
                <div class="section-title">
                    <h4 class="m-0 text-uppercase font-weight-bold">Sedang Panas</h4>
                    <a class="text-secondary font-weight-medium text-decoration-none" href="">Lebih banyak..</a>
                </div>
                <div class="carousel-blur-overlay">
                    <div class="carousel-blur-right"></div>
                    <div class="owl-carousel news-carousel carousel-item-5 position-relative">
                        @for ($i = 0; $i < 3; $i++)
                            <div class="position-relative mb-3">
                                <img class="img-fluid rounded" src="https://img.youtube.com/vi/k-J9BVBjK3o/sddefault.jpg"
                                    style="object-fit: cover">
                                <div class="bg-white border border-top-0 p-4">
                                    <div class="mb-2">
                                        <a class="badge badge-primary text-uppercase font-weight-semi-bold p-2 mr-2"
                                            href="">Category</a>
                                        <a class="text-body" href=""><small>Jan 01, 2045</small></a>
                                    </div>
                                    <a class="h5 d-none d-lg-block mb-0 text-secondary text-uppercase font-weight-bold"
                                        href="">Lorem ipsum dolor sit amet elit...</a>
                                    <a class="small d-block d-lg-none mb-0 text-secondary text-uppercase font-weight-bold"
                                        href="">Lorem ipsum dolor sit amet elit...</a>
                                </div>
                                <div class="d-flex justify-content-between bg-white border border-top-0 p-4">
                                    <div class="d-flex align-items-center">

                                        <small>John Doe</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <small class="ml-3"><i class="far fa-eye mr-2"></i>12345</small>
                                        <small class="ml-3"><i class="far fa-comment mr-2"></i>123</small>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Sedang panas with Carousel End -->

    {{-- pagination start --}}
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="section-title">
                <h4 class="m-0 text-uppercase font-weight-bold">Terbaru</h4>
                <a class="text-secondary font-weight-medium text-decoration-none" href="">Lebih banyak..</a>
            </div>

            <div class="row">
                @foreach ($news as $item)
                    <div class="col-lg-2 col-6 mb-4">
                        <div class="position-relative mb-3">
                            <img class="img-fluid rounded" src="https://img.youtube.com/vi/k-J9BVBjK3o/sddefault.jpg"
                                alt="news image" style="object-fit: cover">
                            <div class="bg-white border p-4">
                                <div class="mb-2">
                                    <a class="badge badge-primary text-uppercase font-weight-semi-bold p-2 mr-2"
                                        href="">{{ $item->category->name }}</a>
                                    <a class="text-body" href=""><small>{{ $item->created_at }}</small></a>
                                </div>
                                <a class="h5 text-secondary font-weight-bold" href="">{{ $item->title }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tampilkan Pagination Links -->
            <div class="d-flex justify-content-center mt-4">
                <ul class="pagination pagination-lg">
                    {{ $news->links() }}
                </ul>
            </div>
        </div>
    </div>
    {{-- pagination end --}}



    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-square back-to-top"><i class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('template/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('template/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('template/js/main.js') }}"></script>
</body>

</html>
