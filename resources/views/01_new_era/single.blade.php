<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>News HTML-5 Template </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="manifest" href="site.webmanifest">
    {{-- <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png"> --}}
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/slicknav.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/responsive.css') }}">


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

<body>
    <header>
        <!-- Header Start -->
        <div class="header-area">
            <div class="main-header ">
                <div class="header-mid d-none d-md-block">
                    <div class="container">
                        <div class="row d-flex align-items-center">
                            <!-- Logo -->
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <div class="logo">
                                    <a href="index.html"><img style="max-height:70px !important;"
                                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRNgvCI7tPdLCgzXEXslMh59BYrnMsdWn1xDQ&s"alt=""></a>
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9 col-md-9">
                                <div class="header-banner f-right ">
                                    <img src="{{ asset('template/assets/img/hero/header_card.jpg') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header-bottom header-sticky">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-xl-10 col-lg-10 col-md-12 header-flex">
                                <!-- sticky -->
                                <div class="sticky-logo">
                                    <a href="index.html"><img style="max-height:70px !important;"
                                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRNgvCI7tPdLCgzXEXslMh59BYrnMsdWn1xDQ&s"
                                            alt=""></a>
                                </div>
                                <!-- Main-menu -->
                                <div class="main-menu d-none d-md-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a href="#">Beranda</a></li>
                                            <li><a href="">Berita Terbaru</a></li>
                                            <li><a href="">Kategori</a></li>
                                            <li><a href="">Tentang Kami</a></li>
                                            <li><a href="">Kontak</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <!-- Mobile Menu -->
                            <div class="col-12">
                                <div class="mobile_menu d-block d-md-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>

    <!--================Blog Area =================-->
    <section class="blog_area single-post-area pt-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 posts-list">
                    <div class="single-post">
                        <div class="feature-img">
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="embed-responsive-item"
                                    src="https://www.youtube.com/embed/{{ $news->content_url }}"
                                    allowfullscreen></iframe>
                            </div>
                        </div>
                        <div class="blog_details">
                            <h2>{{ $news->title }}
                            </h2>
                            <ul class="blog-info-link mt-3 mb-4">
                                <li><a href="{{ route('page.filter', ['user' => $news->user_id]) }}"><i class="fa fa-user"></i>{{ $news->user->name }}</a></li>
                                <li><i class="fa fa-clock"></i>{{ $news->created_at }}</li>
                            </ul>
                            <p class="excert">
                                {{ $news->description }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog_right_sidebar">
                        <aside class="single_sidebar_widget popular_post_widget" style="padding: 20px!important;">
                            <h3 class="widget_title">Postinga Serupa</h3>
                            @foreach ($related_news as $item)
                                <div class="media post_item">
                                    <img style="max-height: 90px;"
                                        src="https://img.youtube.com/vi/{{ $item->content_url }}/hqdefault.jpg"
                                        alt="post">
                                    <div class="media-body">
                                        <a href="{{ route('news.show', $item->id) }}">
                                            <h3>{{ \Illuminate\Support\Str::limit($item->title, 40) }}</h3>
                                        </a>
                                        <p>{{ $item->created_at }}</p>
                                    </div>
                                </div>
                            @endforeach

                        </aside>
                        <aside class="single_sidebar_widget">
                            <h4 class="widget_title">Tagar</h4>
                            
                                @foreach ($news->tags as $tag)
                                    <a class="text-dark ml-3" href="{{ route('page.filter', ['tag' => $tag->name]) }}">#{{$tag->name}}</a>
                                @endforeach
                            
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================ Blog Area end =================-->

    <footer>
        <!-- Footer Start-->
        <div class="footer-area footer-padding fix">
            <div class="container">
                <div class="row d-flex justify-content-between">
                    <div class="col-xl-5 col-lg-5 col-md-7 col-sm-12">
                        <div class="single-footer-caption">
                            <div class="single-footer-caption">
                                <!-- logo -->
                                <div class="footer-logo">
                                    <a href="index.html"><img src="assets/img/logo/logo2_footer.png"
                                            alt=""></a>
                                </div>
                                <div class="footer-tittle">
                                    <div class="footer-pera">
                                        <p>Suscipit mauris pede for con sectetuer sodales adipisci for cursus fames
                                            lectus tempor da blandit gravida sodales Suscipit mauris pede for con
                                            sectetuer sodales adipisci for cursus fames lectus tempor da blandit gravida
                                            sodales Suscipit mauris pede for sectetuer.</p>
                                    </div>
                                </div>
                                <!-- social -->
                                <div class="footer-social">
                                    <a href="#"><i class="fab fa-twitter"></i></a>
                                    <a href="#"><i class="fab fa-instagram"></i></a>
                                    <a href="#"><i class="fab fa-pinterest-p"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4  col-sm-6">
                        <div class="single-footer-caption mt-60">
                            <div class="footer-tittle">
                                <h4>Newsletter</h4>
                                <p>Heaven fruitful doesn't over les idays appear creeping</p>
                                <!-- Form -->
                                <div class="footer-form">
                                    <div id="mc_embed_signup">
                                        <form target="_blank"
                                            action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01"
                                            method="get" class="subscribe_form relative mail_part">
                                            <input type="email" name="email" id="newsletter-form-email"
                                                placeholder="Email Address" class="placeholder hide-on-focus"
                                                onfocus="this.placeholder = ''"
                                                onblur="this.placeholder = ' Email Address '">
                                            <div class="form-icon">
                                                <button type="submit" name="submit" id="newsletter-submit"
                                                    class="email_icon newsletter-submit button-contactForm"><img
                                                        src="assets/img/logo/form-iocn.png" alt=""></button>
                                            </div>
                                            <div class="mt-10 info"></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-6">
                        <div class="single-footer-caption mb-50 mt-60">
                            <div class="footer-tittle">
                                <h4>Instagram Feed</h4>
                            </div>
                            <div class="instagram-gellay">
                                <ul class="insta-feed">
                                    <li><a href="#"><img src="assets/img/post/instra1.jpg" alt=""></a>
                                    </li>
                                    <li><a href="#"><img src="assets/img/post/instra2.jpg" alt=""></a>
                                    </li>
                                    <li><a href="#"><img src="assets/img/post/instra3.jpg" alt=""></a>
                                    </li>
                                    <li><a href="#"><img src="assets/img/post/instra4.jpg" alt=""></a>
                                    </li>
                                    <li><a href="#"><img src="assets/img/post/instra5.jpg" alt=""></a>
                                    </li>
                                    <li><a href="#"><img src="assets/img/post/instra6.jpg" alt=""></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- footer-bottom aera -->
        <div class="footer-bottom-area">
            <div class="container">
                <div class="footer-border">
                    <div class="row d-flex align-items-center justify-content-between">
                        <div class="col-lg-6">
                            <div class="footer-copy-right">
                                <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                    Copyright &copy;
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script> All rights reserved | This template is made with <i
                                        class="ti-heart" aria-hidden="true"></i> by <a href="https://colorlib.com"
                                        target="_blank">Colorlib</a>
                                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="footer-menu f-right">
                                <ul>
                                    <li><a href="#">Terms of use</a></li>
                                    <li><a href="#">Privacy Policy</a></li>
                                    <li><a href="#">Contact</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End-->
    </footer>

    <!-- JS here -->

    <!-- All JS Custom Plugins Link Here here -->
    <script src="{{ asset('template/./assets/js/vendor/modernizr-3.5.0.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/bootstrap.min.js') }}"></script>

    <!-- Jquery, Popper, Bootstrap -->
    <script src="{{ asset('template/./assets/js/vendor/jquery-1.12.4.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/bootstrap.min.js') }}"></script>

    <!-- Jquery Mobile Menu -->
    <script src="{{ asset('template/./assets/js/jquery.slicknav.min.js') }}"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="{{ asset('template/./assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/slick.min.js') }}"></script>
    <!-- Date Picker -->
    <script src="{{ asset('template/./assets/js/gijgo.min.js') }}"></script>
    <!-- One Page, Animated-HeadLin -->
    <script src="{{ asset('template/./assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('template/./assets/js/animated.headline.js') }}"></script>
    <script src="{{ asset('template/./assets/js/jquery.magnific-popup.js') }}"></script>

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

</body>

</html>
