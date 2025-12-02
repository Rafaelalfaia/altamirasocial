<section id="home" class="banner slide-bg bottom-curve">
    <div class="container">
        <div class="row align-items-center">

            {{-- Texto à esquerda --}}
            <div class="col-12 col-md-7 col-lg-6 res-margin">
                <div class="banner-text">
                    <h1 class="wow fadeInUp" data-wow-offset="10" data-wow-duration="1s" data-wow-delay="0s">
                        Sistema SEMAPS <br class="d-none d-xl-block">Inovação &amp; Tecnologia
                    </h1>
                    <p class="wow fadeInUp" data-wow-offset="10" data-wow-duration="1s" data-wow-delay="0.3s">
                        Agora toda sua vida social estará na palma de suas mãos: Cartão Cidadão, Programas Sociais, Cadastro Cidadão, Restaurante Pupular e muito mais.
                    </p>

                    <div class="button-store wow fadeInUp d-flex flex-wrap gap-3 mt-4"
     data-wow-offset="10" data-wow-duration="1s" data-wow-delay="0.6s">

                    {{-- Botão Criar Conta --}}
                    <a href="{{ route('register') }}" class="custom-btn d-inline-flex align-items-center px-4 py-2 bg-white text-green-900 border border-green-800 rounded shadow-sm hover:bg-green-800 hover:text-white transition">
                        <i class="fas fa-user-plus me-2 fs-5"></i>
                        <div class="text-start">
                            <strong>Crie sua Conta</strong><br>
                            <small>Acesso completo</small>
                        </div>
                    </a>

                    {{-- Botão Entrar --}}
                    <a href="{{ route('login') }}" class="custom-btn d-inline-flex align-items-center px-4 py-2 bg-green-800 text-white border border-green-900 rounded shadow-sm hover:bg-white hover:text-green-900 transition">
                        <i class="fas fa-sign-in-alt me-2 fs-5"></i>
                        <div class="text-start">
                            <strong>Entrar</strong><br>
                            <small>Já sou cadastrado</small>
                        </div>
                    </a>

                </div>


                </div>
            </div>

            {{-- Imagem à direita --}}
            <div class="col-12 col-md-5 col-lg-6">
                <div class="banner-image-center wow fadeInUp" data-wow-offset="10" data-wow-duration="1s" data-wow-delay="0.3s">
                    <img src="{{ asset('images/banner/slide-welcome.png') }}" alt="App Preview" />
                </div>
            </div>

        </div>
    </div>
</section>
