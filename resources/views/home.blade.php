@extends('layouts.site')

@section('title', 'SEMAPS - Sistema e App')

@section('content')
    {{-- Loader --}}
    <div class="page-loader">
        <div class="progress"></div>
    </div>

    {{-- Header --}}
    <header id="top-page" class="header">
        @include('components.navbar')
    </header>

    {{-- Search Wrapper --}}
    <div class="search-wrapper">
        <form role="search" method="get" class="search-form" action="#">
            <input type="search" name="s" id="s"
                   placeholder="Search Keyword"
                   class="searchbox-input" autocomplete="off" required />
            <span>Input your search keywords and press Enter.</span>
        </form>
        <div class="search-wrapper-close">
            <span class="search-close-btn"></span>
        </div>
    </div>

    {{-- Banner --}}
    @include('home.sections.banner')

    {{-- Services --}}
    @include('home.sections.services')

    {{-- Features --}}
    @include('home.sections.features')

     {{-- Parallax Video --}}
    @include('home.sections.video')


    {{-- Overview --}}
    @include('home.sections.overview')



    {{-- Footer --}}
    @include('components.footer')

    {{-- Back to Top --}}
    <a href="#top-page" class="to-top">
        <div class="icon icon-arrows-up"></div>
    </a>
@endsection

@push('styles')
    {{-- Fonts e CSS --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:200,300,400,500,700">
    <link rel="stylesheet" href="{{ asset('assets/library/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/linea/arrows/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/linea/basic/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/linea/ecommerce/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/linea/software/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/linea/weather/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/lightcase/css/lightcase.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/owlcarousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/magnificpopup/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/library/ytplayer/css/jquery.mb.ytplayer.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/media.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/colors/turquoise.css') }}" class="colors">
@endpush

@push('scripts')
    {{-- JS --}}
    <script src="{{ asset('assets/library/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/library/jquery/jquery-easing.js') }}"></script>
    <script src="{{ asset('assets/library/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/library/retina/retina.min.js') }}"></script>
    <script src="{{ asset('assets/library/backstretch/jquery.backstretch.min.js') }}"></script>
    <script src="{{ asset('assets/library/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/library/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/library/slick/slick.js') }}"></script>
    <script src="{{ asset('assets/library/waypoints/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/library/isotope/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/library/waitforimages/jquery.waitforimages.min.js') }}"></script>
    <script src="{{ asset('assets/library/lightcase/js/lightcase.js') }}"></script>
    <script src="{{ asset('assets/library/wow/wow.min.js') }}"></script>
    <script src="{{ asset('assets/library/parallax/jquery.parallax.min.js') }}"></script>
    <script src="{{ asset('assets/library/counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/library/magnificpopup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/library/ytplayer/jquery.mb.ytplayer.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
@endpush
