<div id="mainNav" class="main-menu-area animated">
    <div class="container">
        <div class="row align-items-center">

            {{-- Logo e menu mobile --}}
            <div class="col-12 col-lg-2 d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a class="navbar-brand navbar-brand1" href="#top-page">
                        <img src="{{ asset('images/logo-white.png') }}" alt="SEMAPS" />
                    </a>
                    <a class="navbar-brand navbar-brand2" href="#top-page">
                        <img src="{{ asset('images/logo.png') }}" alt="SEMAPS" />
                    </a>
                </div>

                {{-- Menu Hamburguer mobile --}}
                <div class="menu-bar d-lg-none">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Navegação --}}
            <div class="op-mobile-menu col-lg-10 p-0 d-lg-flex align-items-center justify-content-end">
                {{-- Header mobile --}}
                <div class="m-menu-header d-flex justify-content-between align-items-center d-lg-none">
                    <a href="#" class="logo">
                        <img src="{{ asset('images/logo.png') }}" alt="SEMAPS" />
                    </a>
                    <span class="close-button"></span>
                </div>

                {{-- Links --}}
                <ul class="nav-menu d-lg-flex flex-wrap list-unstyled justify-content-center">
                    <li class="nav-item"><a class="nav-link js-scroll-trigger active" href="#top-page"><span>Início</span></a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#services"><span>Como Funciona</span></a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#features"><span>Recursos</span></a></li>
                    <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#overview"><span>Novidade</span></a></li>

                    {{-- Botão Criar Conta --}}
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="nav-link navbar-auth-btn me-2">
                            Criar Conta
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link navbar-auth-btn">
                            Entrar
                        </a>
                    </li>

                </ul>

            </div>
        </div>
    </div>
</div>
