<section id="testimonials">
    <div class="container">

        {{-- Título da seção --}}
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-6">
                <div class="section-title text-center">
                    <h3>O que dizem sobre o SEMAPS</h3>
                    <p>Depoimentos de pessoas que vivenciam o impacto do Sistema Municipal de Acompanhamento e Programas Sociais.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 testimonial-carousel">

                {{-- Textos --}}
                <div class="block-text row">
                    <div class="carousel-text testimonial-slider col-12 col-lg-8 offset-lg-2">

                        {{-- Depoimento 1 --}}
                        <div>
                            <div class="single-box">
                                <p><i class="fas fa-quote-left"></i>
                                    Graças ao SEMAPS consegui me inscrever em um programa de alimentação e recebi apoio da assistente social durante toda a pandemia.
                                    <i class="fas fa-quote-right"></i>
                                </p>
                            </div>
                        </div>

                        {{-- Depoimento 2 --}}
                        <div>
                            <div class="single-box">
                                <p><i class="fas fa-quote-left"></i>
                                    A plataforma é clara e eficiente. Como coordenadora, consigo acompanhar cada cidadão e otimizar os recursos disponíveis.
                                    <i class="fas fa-quote-right"></i>
                                </p>
                            </div>
                        </div>

                        {{-- Depoimento 3 --}}
                        <div>
                            <div class="single-box">
                                <p><i class="fas fa-quote-left"></i>
                                    O sistema me ajudou a organizar os acompanhamentos e registrar a evolução das famílias com clareza e responsabilidade.
                                    <i class="fas fa-quote-right"></i>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Imagens --}}
                <div class="block-media row">
                    <div class="carousel-images testimonial-nav col-12 col-lg-8 offset-lg-2">

                        {{-- Pessoa 1 --}}
                        <div>
                            <img src="{{ asset('images/testimonials/client-1.jpg') }}" alt="Cidadã" class="img-fluid rounded-circle">
                            <div class="client-info">
                                <h3>Maria da Conceição</h3>
                                <span>Bairro Independência</span>
                            </div>
                        </div>

                        {{-- Pessoa 2 --}}
                        <div>
                            <img src="{{ asset('images/testimonials/client-2.jpg') }}" alt="Coordenadora" class="img-fluid rounded-circle">
                            <div class="client-info">
                                <h3>Renata Souza</h3>
                                <span>Coordenadora de Programas</span>
                            </div>
                        </div>

                        {{-- Pessoa 3 --}}
                        <div>
                            <img src="{{ asset('images/testimonials/client-3.jpg') }}" alt="Assistente" class="img-fluid rounded-circle">
                            <div class="client-info">
                                <h3>Eduardo Lima</h3>
                                <span>Assistente Social</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
