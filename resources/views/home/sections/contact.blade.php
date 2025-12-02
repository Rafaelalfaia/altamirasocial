<section id="contact">
    <div class="container">

        {{-- Título --}}
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-6">
                <div class="section-title text-center">
                    <h3>Fale com a Assistência Social</h3>
                    <p>Entre em contato com a equipe responsável pelo SEMAPS para dúvidas, sugestões ou apoio técnico.</p>
                </div>
            </div>
        </div>

        <div class="row">

            {{-- Informações de contato --}}
            <div class="contact-info col-12 col-lg-4 res-margin">
                <h5>
                    <span class="icon icon-basic-geolocalize-05"></span> 
                    Endereço
                </h5>
                <p>
                    Secretaria de Assistência Social<br>
                    Av. Central, nº 100<br>
                    Altamira - PA
                </p>

                <h5>
                    <span class="icon icon-basic-smartphone"></span> 
                    Telefone
                </h5>
                <p><a href="tel:+559399999999">+55 (93) 99999-9999</a></p>

                <h5>
                    <span class="icon icon-basic-mail"></span> 
                    E-mail
                </h5>
                <p>
                    <a href="mailto:contato@semaps.altamira.pa.gov.br">contato@semaps.altamira.pa.gov.br</a>
                </p>

                <h5>
                    <span class="icon icon-basic-clock"></span> 
                    Atendimento
                </h5>
                <p>
                    Segunda a Sexta<br>
                    08h às 17h
                </p>
            </div>

            {{-- Formulário --}}
            <div class="col-12 col-lg-8">
                <form id="contact-form" action="#" method="post">                             
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group mt-2 mb-3">
                                <input type="text" name="name" class="form-control field-name" placeholder="Nome completo" required>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group mt-2 mb-3">
                                <input type="email" name="email" class="form-control field-email" placeholder="E-mail" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mt-2 mb-3">
                                <input type="text" name="subject" class="form-control field-subject" placeholder="Assunto" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mt-2 mb-3">
                                <textarea name="message" rows="4" class="form-control field-message" placeholder="Mensagem" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mt-2 mb-3">
                            <button type="submit" id="contact-submit" class="btn">Enviar Mensagem</button>
                        </div>
                    </div>
                </form>

                {{-- Mensagem de sucesso (placeholder) --}}
                <div class="contact-form-result">
                    <h4>Mensagem enviada com sucesso!</h4>
                    <p>A equipe do SEMAPS entrará em contato o mais breve possível.</p>
                </div>
            </div>

        </div>
    </div>
</section>
