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
                                        <li class="{{ request()->routeIs('news.index') ? 'nav-active' : '' }}">
                                            <a href="{{ route('news.index') }}">Beranda</a>
                                        </li>
                                        <li class="{{ request()->routeIs('page.latest') ? 'nav-active' : '' }}">
                                            <a href="{{ route('page.latest') }}">Berita Terbaru</a>
                                        </li>
                                        <li class="{{ request()->has('category') ? 'nav-active' : '' }}">
                                            <a href="#">Kategori</a>
                                            <ul class="submenu">
                                                @foreach ($categories as $c)
                                                    <li
                                                        class="{{ request()->fullUrlIs(route('page.filter', ['category' => $c->slug])) ? 'nav-active' : '' }}">
                                                        <a
                                                            href="{{ route('page.filter', ['category' => $c->slug]) }}">{{ $c->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                        <li class="{{ request()->routeIs('page.about') ? 'nav-active' : '' }}">
                                            <a href="{{ route('page.about') }}">Tentang Kami</a>
                                        </li>
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
