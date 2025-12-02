<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers comuns
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\DB;
use App\Models\Programa;

// Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UsuarioAdminController;
use App\Http\Controllers\Admin\RelatorioAdminController;
use App\Http\Controllers\Admin\CidadaoAdminController;
use App\Http\Controllers\Admin\AssistenteAdminController;
use App\Http\Controllers\Admin\CoordenadorAdminController;
use App\Http\Controllers\Admin\ProgramaAdminController;
use App\Http\Controllers\Admin\InscricaoAdminController;
use App\Http\Controllers\Admin\EvolucaoAdminController;
use App\Http\Controllers\Admin\EmergenciaAdminController;
use App\Http\Controllers\Admin\DenunciaAdminController;
use App\Http\Controllers\Admin\IndicacaoAdminController;
use App\Http\Controllers\Admin\RestauranteAdminController;
use App\Models\User;
use App\Http\Controllers\Admin\UsuarioController;


// Coordenador
use App\Http\Controllers\Coordenador\CoordenadorController;
use App\Http\Controllers\Coordenador\CidadaoInternoController;
use App\Http\Controllers\Coordenador\ProgramaController;
use App\Http\Controllers\Coordenador\LotePagamentoController;
use App\Http\Controllers\Coordenador\MoradiaController;
use App\Http\Controllers\Coordenador\SolicitacaoController;
use App\Http\Controllers\Coordenador\AssistenteSocialController;
use App\Http\Controllers\Coordenador\OrgaoPublicoController;
use App\Http\Controllers\Coordenador\RecebimentoEncaminhamentoController;
use App\Http\Controllers\Coordenador\ProgramaAnaliseController;
use App\Http\Controllers\Coordenador\EmergenciaCoordenadorController;
use App\Http\Controllers\Coordenador\AcompanhamentoAssistenteController;
use App\Http\Controllers\Coordenador\RelatorioController;
use App\Http\Controllers\Coordenador\RankingAssistenteController;
use Illuminate\Support\Facades\Hash;



// Cidadão
use App\Http\Controllers\Cidadao\CidadaoController;
use App\Http\Controllers\Cidadao\PerfilController;
use App\Http\Controllers\Cidadao\PerfilPublicoController;
use App\Http\Controllers\Cidadao\ProgramaPublicoController;
use App\Http\Controllers\Cidadao\ProgramaInscricaoController;
use App\Http\Controllers\Cidadao\SolicitacaoCidadaoController;
use App\Http\Controllers\Cidadao\EmergenciaController;
use App\Http\Controllers\Cidadao\CidadaoRelatorioController;
use App\Http\Controllers\Cidadao\RecuperacaoSenhaController;
use App\Http\Controllers\Cidadao\DependenteController;



// Assistente
use App\Http\Controllers\Assistente\UsuarioController as UsuarioAssistenteController;
use App\Http\Controllers\Assistente\AssistenteController;
use App\Http\Controllers\Assistente\CidadaoAssistenteController;
use App\Http\Controllers\Assistente\ProgramaAssistenteController;
use App\Http\Controllers\Assistente\AcompanhamentoController;
use App\Http\Controllers\Assistente\EvolucaoController;
use App\Http\Controllers\Assistente\SolicitacaoAssistenteController;
use App\Http\Controllers\Assistente\ProgramaAcaoController;
use App\Http\Controllers\Assistente\ModoPlantaoController;
use App\Http\Controllers\Assistente\EmergenciaAssistenteController;
use App\Http\Controllers\Assistente\RelatorioAssistenteController;
use App\Http\Controllers\Cidadao\FichaPublicaController;


//Restaurante

// Coordenador Restaurante
use App\Http\Controllers\Restaurante\Coordenador\DashboardController as CoordenadorDashboardController;
use App\Http\Controllers\Restaurante\Coordenador\RestauranteController;
use App\Http\Controllers\Restaurante\Coordenador\AtendenteController;
use App\Http\Controllers\Restaurante\Coordenador\CidadaoRestauranteController;
use App\Http\Controllers\Restaurante\Coordenador\CidadaoTemporarioController;
use App\Http\Controllers\Restaurante\Coordenador\VendaRestauranteController as VendaCoordenadorController;

use App\Http\Controllers\Restaurante\Coordenador\RelatorioRestauranteController;



// Atendente Restaurante
use App\Http\Controllers\Restaurante\Atendente\DashboardController;

use App\Http\Controllers\Restaurante\Atendente\AtendenteCidadaoController;


use App\Http\Controllers\Restaurante\Atendente\VendaRestauranteController as VendaAtendenteController;
use App\Http\Controllers\Restaurante\Atendente\RelatorioRestauranteController as AtendenteRelatorioController;





Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/dashboard', [RedirectController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

//conferencia
Route::get('/jitsi/teste', fn () => view('jitsi.teste'))->name('jitsi.teste');


// ADMIN
Route::prefix('admin')->middleware(['auth', 'role:Admin'])->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Usuários
    Route::resource('usuarios', UsuarioController::class);
    Route::get('usuarios/relatorio', [UsuarioController::class, 'relatorioPdf'])->name('usuarios.relatorio');
    Route::delete('/admin/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');



    // Cidadãos
    Route::get('cidadaos', [CidadaoAdminController::class, 'index'])->name('cidadaos');
    Route::get('cidadaos/{cidadao}/entrar', [CidadaoAdminController::class, 'entrar'])->name('cidadaos.entrar');

    // Assistentes
    Route::get('assistentes', [AssistenteAdminController::class, 'index'])->name('assistentes');
    Route::get('assistentes/{assistente}/edit', [AssistenteAdminController::class, 'edit'])->name('assistentes.edit');
    Route::put('assistentes/{assistente}', [AssistenteAdminController::class, 'update'])->name('assistentes.update');
    Route::get('assistentes/{assistente}/entrar', [AssistenteAdminController::class, 'entrar'])->name('assistentes.entrar');

    // Coordenadores
    Route::get('coordenadores', [CoordenadorAdminController::class, 'index'])->name('coordenadores.index');
    Route::get('coordenadores/entrar/{id}', [CoordenadorAdminController::class, 'entrar'])->name('coordenadores.entrar');


    Route::resource('cidadaos-temporarios', \App\Http\Controllers\Admin\CidadaoTemporarioController::class)->only(['index', 'show']);


    // Programas sociais
    Route::get('/programas', [ProgramaAdminController::class, 'index'])->name('programas.index');
    Route::get('/programas/{id}', [ProgramaAdminController::class, 'show'])->name('programas.show');
    Route::delete('/programas/{id}', [ProgramaAdminController::class, 'destroy'])->name('programas.destroy');


    // Restaurante Popular
    Route::get('/restaurante', [RestauranteAdminController::class, 'index'])->name('restaurante.index');

    Route::get('restaurante/{restaurante}/entrar', [RestauranteAdminController::class, 'entrar'])->name('restaurante.entrar');


    // Relatórios
    // Página principal de relatórios (dashboard visual)
    Route::get('/relatorios', [RelatorioAdminController::class, 'index'])->name('relatorios.index');

    // ------------------- CIDADÃOS -------------------
    Route::get('/relatorios/crescimento-cidadaos', [RelatorioAdminController::class, 'graficoCrescimentoCidadaos'])->name('relatorios.cidadaos.crescimento');
    Route::get('/relatorios/genero-cidadaos', [RelatorioAdminController::class, 'graficoGeneroCidadaos'])->name('relatorios.cidadaos.genero');
    Route::get('/relatorios/faixa-etaria-cidadaos', [RelatorioAdminController::class, 'graficoFaixaEtariaCidadaos'])->name('relatorios.cidadaos.faixa_etaria');
    Route::get('/relatorios/pcd-cidadaos', [RelatorioAdminController::class, 'graficoPcdCidadaos'])->name('relatorios.cidadaos.pcd');
    Route::get('/relatorios/regiao-cidadaos', [RelatorioAdminController::class, 'graficoCidadaosPorRegiao'])->name('relatorios.cidadaos.regiao');
    Route::get('/relatorios/preenchimento-cadastro', [RelatorioAdminController::class, 'graficoPreenchimentoCadastro'])->name('relatorios.cidadaos.preenchimento');
    Route::get('/relatorios/cidadaos-temporarios', [RelatorioAdminController::class, 'graficoCidadaosTemporarios'])->name('relatorios.cidadaos.temporarios');

    // ------------------- PROGRAMAS SOCIAIS -------------------
    Route::get('/relatorios/inscricoes-por-programa', [RelatorioAdminController::class, 'graficoInscricoesPorPrograma'])->name('relatorios.programas.inscricoes');
    Route::get('/relatorios/status-inscricoes', [RelatorioAdminController::class, 'graficoStatusInscricoes'])->name('relatorios.programas.status');
    Route::get('/relatorios/denuncias-programas', [RelatorioAdminController::class, 'graficoDenunciasPorPrograma'])->name('relatorios.programas.denuncias');
    Route::get('/relatorios/indicacoes-programas', [RelatorioAdminController::class, 'graficoIndicacoesPorPrograma'])->name('relatorios.programas.indicacoes');
    Route::get('/relatorios/regioes-inscricoes', [RelatorioAdminController::class, 'graficoRegioesInscricoes'])->name('relatorios.programas.regioes');
    Route::get('/relatorios/media-renda-programas', [RelatorioAdminController::class, 'graficoMediaRendaBeneficiarios'])->name('relatorios.programas.renda');

    // ------------------- ASSISTENTES E EVOLUÇÕES -------------------
    Route::get('/relatorios/evolucoes-por-assistente', [RelatorioAdminController::class, 'graficoEvolucoesPorAssistente'])->name('relatorios.assistentes.evolucoes');
    Route::get('/relatorios/assistentes-ativos', [RelatorioAdminController::class, 'graficoAssistenteMaisAtivo'])->name('relatorios.assistentes.ativos');
    Route::get('/relatorios/plantao-ativo', [RelatorioAdminController::class, 'graficoPlantaoAtivo'])->name('relatorios.assistentes.plantao');
    Route::get('/relatorios/respostas-solicitacoes', [RelatorioAdminController::class, 'graficoRespostasSolicitacoesAssistente'])->name('relatorios.assistentes.respostas');
    Route::get('/relatorios/ranking-assistentes', [RelatorioAdminController::class, 'graficoRankingAssistentesProdutividade'])->name('relatorios.assistentes.ranking');

    // ------------------- RESTAURANTE E EMERGÊNCIAS -------------------
    Route::get('/relatorios/vendas-por-dia', [RelatorioAdminController::class, 'graficoVendasPorDia'])->name('relatorios.restaurante.vendas');
    Route::get('/relatorios/tipo-consumo', [RelatorioAdminController::class, 'graficoTipoConsumo'])->name('relatorios.restaurante.consumo');
    Route::get('/relatorios/formas-pagamento', [RelatorioAdminController::class, 'graficoFormasPagamento'])->name('relatorios.restaurante.pagamento');

    Route::get('/relatorios/emergencias-por-periodo', [RelatorioAdminController::class, 'graficoEmergenciasPorPeriodo'])->name('relatorios.emergencias.periodo');
    Route::get('/relatorios/encaminhamentos-por-orgao', [RelatorioAdminController::class, 'graficoEncaminhamentosPorOrgao'])->name('relatorios.encaminhamentos.orgao');
});


// Rota para voltar do perfil de cidadão para o coordenador
Route::get('/voltar-coordenador', function () {
    if (session()->has('impersonate_coordenador_id')) {
        $id = session()->pull('impersonate_coordenador_id');
        Auth::loginUsingId($id);
        return redirect()->route('coordenador.dashboard')->with('status', 'Retornado ao coordenador.');
    }
    return redirect('/');
})->name('voltar.coordenador.alternativo')->middleware('auth');



// Rota para voltar do perfil de cidadão para o admin
Route::get('/voltar-admin', function () {
    if (!session()->has('impersonate_admin_id')) {
        return redirect('/');
    }

    $impersonacoes = [
        'impersonate_coordenador_id',
        'impersonate_assistente_id',
        'impersonate_cidadao_id',
        'impersonate_restaurante_id',
    ];

    foreach ($impersonacoes as $chave) {
        if (session()->has($chave)) {
            session()->forget($chave);
            $adminId = session()->pull('impersonate_admin_id');
            auth()->loginUsingId($adminId);
            return redirect()->route('admin.dashboard')->with('status', 'Retornado ao admin.');
        }
    }

    return redirect('/');
})->name('voltar.admin')->middleware('auth');



// COORDENADOR
Route::prefix('coordenador')
    ->middleware(['auth', 'role:Coordenador'])
    ->name('coordenador.')
    ->group(function () {

        Route::get('/dashboard', [CoordenadorController::class, 'index'])->name('dashboard');

        Route::prefix('cidadaos')->name('cidadaos.')->group(function () {
            Route::get('/', [CidadaoInternoController::class, 'index'])->name('index');
            Route::get('/create', [CidadaoInternoController::class, 'create'])->name('create');
            Route::post('/', [CidadaoInternoController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [CidadaoInternoController::class, 'edit'])
                ->whereNumber('id')->name('edit');
            Route::put('/{id}', [CidadaoInternoController::class, 'update'])
                ->whereNumber('id')->name('update');
            Route::delete('/{id}', [CidadaoInternoController::class, 'destroy'])
                ->whereNumber('id')->name('destroy');
            Route::get('/{id}/cartao', [CidadaoInternoController::class, 'cartao'])
                ->whereNumber('id')->name('cartao');
            Route::get('/{id}/entrar', [CidadaoInternoController::class, 'entrarComoCidadao'])
                ->whereNumber('id')->name('entrar');
        });



Route::resource('programas', ProgramaController::class)->names('programas');

Route::prefix('programas/{programa}')
    ->name('programas.')
    ->whereNumber('programa')
    ->group(function () {

        // Listagem/Exportação de inscrições
        Route::get('inscricoes', [ProgramaController::class, 'inscritos'])
            ->name('inscritos');                // coordenador.programas.inscritos
        Route::get('inscricoes/pdf', [ProgramaController::class, 'baixarPdf'])
            ->name('inscritos.pdf');            // coordenador.programas.inscritos.pdf

        // Ação em MASSA
        Route::post('inscricoes/bulk-status', [ProgramaController::class, 'bulkStatus'])
            ->name('inscricoes.bulk-status');   // coordenador.programas.inscricoes.bulk-status

        // Ações INDIVIDUAIS de status
        Route::post('inscricoes/{inscricao}/aprovar', [ProgramaController::class, 'aprovar'])
            ->whereNumber('inscricao')
            ->name('aprovar');                   // coordenador.programas.aprovar
        Route::post('inscricoes/{inscricao}/reprovar', [ProgramaController::class, 'reprovar'])
            ->whereNumber('inscricao')
            ->name('reprovar');                  // coordenador.programas.reprovar
        Route::put('inscricoes/{inscricao}/status', [ProgramaController::class, 'atualizarInscricao'])
            ->whereNumber('inscricao')
            ->name('atualizar-inscricao');       // coordenador.programas.atualizar-inscricao

        // Editar/Atualizar/Excluir inscrição
        Route::get('inscricoes/{inscricao}/edit', [ProgramaController::class, 'editInscricao'])
            ->whereNumber('inscricao')
            ->name('inscricoes.edit');           // coordenador.programas.inscricoes.edit
        Route::put('inscricoes/{inscricao}', [ProgramaController::class, 'updateInscricao'])
            ->whereNumber('inscricao')
            ->name('inscricoes.update');         // coordenador.programas.inscricoes.update
        Route::delete('inscricoes/{inscricao}', [ProgramaController::class, 'destroyInscricao'])
            ->whereNumber('inscricao')
            ->name('inscricoes.destroy');
            Route::get('inscricoes/{inscricao}', [ProgramaController::class, 'showInscricao'])
            ->whereNumber('inscricao')
            ->name('inscricoes.show');
            Route::post('inscricoes/pdf-selecionados', [ProgramaController::class, 'baixarPdfSelecionados'])
            ->name('inscricoes.pdf.selecionados');

    });



    Route::get('/plantoes/historico', [CoordenadorController::class, 'historicoPlantoes'])->name('plantoes.historico');


    Route::resource('lotes', LotePagamentoController::class);
    Route::get('lotes/{lote}/baixar', [LotePagamentoController::class, 'baixar'])->name('lotes.baixar');
    Route::prefix('moradia')->group(function () {
        Route::get('/', [MoradiaController::class, 'index'])->name('moradia.index');
        Route::post('estado', [MoradiaController::class, 'salvarEstado'])->name('moradia.estado.salvar');
        Route::post('cidade', [MoradiaController::class, 'salvarCidade'])->name('moradia.cidade.salvar');
        Route::post('bairro', [MoradiaController::class, 'salvarBairro'])->name('moradia.bairro.salvar');
        Route::delete('bairro/{bairro}', [MoradiaController::class, 'deletarBairro'])->name('moradia.bairro.deletar');
        Route::delete('estado/{estado}', [MoradiaController::class, 'deletarEstado'])->name('moradia.estado.deletar');
        Route::delete('cidade/{cidade}', [MoradiaController::class, 'deletarCidade'])->name('moradia.cidade.deletar');
    });



   // Listar assistentes sociais criados por este coordenador
    Route::get('/assistentes', [AssistenteSocialController::class, 'index'])->name('assistentes.index');

    Route::get('/assistentes/criar', [AssistenteSocialController::class, 'create'])->name('assistentes.create');
    Route::post('/assistentes', [AssistenteSocialController::class, 'store'])->name('assistentes.store');
    Route::get('/assistentes/{id}/entrar', [AssistenteSocialController::class, 'entrar'])->name('assistentes.entrar');


    Route::get('assistentes/{id}/editar', [AssistenteSocialController::class, 'edit'])->name('assistentes.edit');

    // Atualização do assistente (PATCH)
    Route::patch('assistentes/{id}', [AssistenteSocialController::class, 'update'])->name('assistentes.update');

    Route::get('assistentes/{id}/acompanhamentos', [AcompanhamentoAssistenteController::class, 'index'])
    ->name('assistentes.acompanhamentos');

        Route::get('assistentes/{assistente}/acompanhamentos/{acompanhamento}', [AcompanhamentoAssistenteController::class, 'show'])
        ->name('assistentes.acompanhamentos.show');

        Route::get('assistentes/{assistente}/acompanhamentos/{acompanhamento}/evolucoes', [AcompanhamentoAssistenteController::class, 'evolucoesIndex'])
        ->name('assistentes.acompanhamentos.evolucoes.index');




    // COORDENADOR - Solicitações
    Route::prefix('solicitacoes')->name('solicitacoes.')->group(function () {
        Route::get('/', [SolicitacaoController::class, 'index'])->name('index');
        Route::get('/criar', [SolicitacaoController::class, 'create'])->name('create');
        Route::post('/', [SolicitacaoController::class, 'store'])->name('store');
        Route::get('/{solicitacao}', [SolicitacaoController::class, 'show'])->name('show');
        Route::get('/{solicitacao}/editar', [SolicitacaoController::class, 'edit'])->name('edit');
        Route::put('/{solicitacao}', [SolicitacaoController::class, 'update'])->name('update');
        Route::patch('/{solicitacao}/cancelar', [SolicitacaoController::class, 'cancelarEnvio'])->name('cancelar');
        Route::delete('/{solicitacao}', [SolicitacaoController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('orgaos')->name('orgaos.')->group(function () {
        Route::get('/', [OrgaoPublicoController::class, 'index'])->name('index');
        Route::get('/create', [OrgaoPublicoController::class, 'create'])->name('create');
        Route::post('/', [OrgaoPublicoController::class, 'store'])->name('store');
        Route::get('/{orgao}/edit', [OrgaoPublicoController::class, 'edit'])->name('edit');
        Route::put('/{orgao}', [OrgaoPublicoController::class, 'update'])->name('update');
        Route::delete('/{orgao}', [OrgaoPublicoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('recebimentos')->name('recebimentos.')->group(function () {
        Route::get('/', [RecebimentoEncaminhamentoController::class, 'index'])->name('index');
        Route::get('/create', [RecebimentoEncaminhamentoController::class, 'create'])->name('create');
        Route::post('/', [RecebimentoEncaminhamentoController::class, 'store'])->name('store');
        Route::get('/{id}', [RecebimentoEncaminhamentoController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [RecebimentoEncaminhamentoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RecebimentoEncaminhamentoController::class, 'update'])->name('update');
        Route::delete('/{id}', [RecebimentoEncaminhamentoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('analises')->name('analises.')->group(function () {
        Route::get('/', [ProgramaAnaliseController::class, 'index'])->name('index');
        Route::get('/historico', [ProgramaAnaliseController::class, 'historico'])->name('historico');
        Route::post('/{tipo}/{id}/aceitar', [ProgramaAnaliseController::class, 'aceitar'])->name('aceitar');
        Route::post('/{tipo}/{id}/recusar', [ProgramaAnaliseController::class, 'recusar'])->name('recusar');
    });

    Route::get('/assistentes/ranking', [RankingAssistenteController::class, 'index'])
    ->name('assistentes.ranking.index');



    Route::get('/emergencias', [EmergenciaCoordenadorController::class, 'index'])->name('emergencias.index');
    Route::get('/emergencias/historico', [EmergenciaCoordenadorController::class, 'historico'])->name('emergencias.historico');
    Route::get('/emergencias/{id}', [EmergenciaCoordenadorController::class, 'show'])->name('emergencias.show');
    Route::delete('/emergencias/{id}', [EmergenciaCoordenadorController::class, 'destroy'])->name('emergencias.destroy');

    Route::get('relatorios/grafico/pessoas-deficiencia', [RelatorioController::class, 'graficoPessoasComDeficiencia'])
            ->name('relatorios.grafico.pessoas_deficiencia');

    Route::get('relatorios/grafico/participacao-programas', [RelatorioController::class, 'graficoParticipacaoProgramas'])
            ->name('relatorios.grafico.participacao_programas');


    Route::prefix('relatorios')
            ->name('relatorios.')
            ->group(function () {
                Route::get('/', [RelatorioController::class, 'index'])->name('index');
            });

    Route::get('relatorios/grafico/pcds-por-programa', [RelatorioController::class, 'graficoPCDsPorPrograma'])
            ->name('relatorios.grafico.pcds_por_programa');




});

Route::get('/voltar-para-coordenador', function () {
    if (session()->has('impersonate_coordenador_id')) {
        $id = session()->pull('impersonate_coordenador_id');
        $user = \App\Models\User::find($id);
        if ($user) {
            Auth::login($user);
            return redirect()->route('coordenador.dashboard')->with('status', 'Você voltou ao painel do Coordenador.');
        }
    }
    return redirect('/')->with('error', 'Sessão de coordenador não encontrada.');
})->name('voltar.coordenador');

// ASSISTENTE
Route::prefix('assistente')->middleware(['auth', 'role:Assistente'])->name('assistente.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AssistenteController::class, 'index'])->name('dashboard');
    Route::post('/modo-plantao', [ModoPlantaoController::class, 'alternar'])->name('modo-plantao');
    Route::get('/emergencias', [EmergenciaAssistenteController::class, 'index'])->name('emergencias.index');
    Route::get('/emergencias/chamada/{sala}', [EmergenciaAssistenteController::class, 'chamada'])->name('emergencias.chamada');
    Route::get('/emergencias/{id}/relatar', [EmergenciaAssistenteController::class, 'formRelatar'])->name('emergencias.relatar');
       Route::post('/emergencias/{id}/relatar', [EmergenciaAssistenteController::class, 'enviarRelatorio'])
    ->name('emergencias.enviar-relatorio');

    Route::delete('/emergencias/{id}', [EmergenciaAssistenteController::class, 'destroy'])->name('emergencias.destroy');

    Route::patch('/emergencias/{emergencia}/finalizar', [EmergenciaAssistenteController::class, 'finalizar'])->name('emergencias.finalizar');

    Route::get('/usuarios', [UsuarioAssistenteController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/{id}', [UsuarioAssistenteController::class, 'show'])->name('usuarios.show');

    // CIDADÃO - CRUD Assistente
    Route::prefix('cidadao')->name('cidadao.')->group(function () {
        Route::get('criar', [CidadaoAssistenteController::class, 'criar'])->name('criar');
        Route::post('criar', [CidadaoAssistenteController::class, 'salvar'])->name('salvar');
        Route::delete('{id}', [CidadaoAssistenteController::class, 'destroy'])->name('destroy');
        Route::get('{id}/editar/dados', [CidadaoAssistenteController::class, 'editarDados'])->name('dados.editar');
        Route::post('{id}/editar/dados', [CidadaoAssistenteController::class, 'atualizarDados'])->name('dados.salvar');
        Route::get('{id}/editar/moradia', [CidadaoAssistenteController::class, 'editarMoradia'])->name('moradia.editar');
        Route::post('{id}/editar/moradia', [CidadaoAssistenteController::class, 'atualizarMoradia'])->name('moradia.salvar');
        Route::get('{id}/editar/trabalho', [CidadaoAssistenteController::class, 'editarTrabalho'])->name('trabalho.editar');
        Route::post('{id}/editar/trabalho', [CidadaoAssistenteController::class, 'atualizarTrabalho'])->name('trabalho.salvar');
        Route::get('{id}/editar/acessibilidade', [CidadaoAssistenteController::class, 'editarAcessibilidade'])->name('acessibilidade.editar');
        Route::post('{id}/editar/acessibilidade', [CidadaoAssistenteController::class, 'atualizarAcessibilidade'])->name('acessibilidade.salvar');
        Route::get('{id}/alterar-senha', [CidadaoAssistenteController::class, 'senha'])->name('senha');
        Route::post('{id}/alterar-senha', [CidadaoAssistenteController::class, 'atualizarSenha'])->name('senha.atualizar');
        Route::get('{id}/cartao', [CidadaoAssistenteController::class, 'cartao'])->name('cartao');

    });

        // Programas sociais
        Route::prefix('programas')->name('programas.')->group(function () {

            Route::get('/', [ProgramaAssistenteController::class, 'index'])->name('index');
            Route::get('{programa}/indicar/{cidadao}', [ProgramaAcaoController::class, 'criarIndicacao'])->name('indicar.form');
            Route::post('{programa}/indicar/{cidadao}', [ProgramaAcaoController::class, 'salvarIndicacao'])->name('indicar.store');
            Route::get('{programa}/indicar', [ProgramaAcaoController::class, 'indicar'])->name('indicar');
            Route::post('{programa}/indicar/{cidadao}', [ProgramaAcaoController::class, 'salvarIndicacao'])->name('indicar.acao');
            Route::get('{programa}/denunciar', [ProgramaAcaoController::class, 'denunciar'])->name('denunciar');
            Route::get('{programa}/denunciar/{cidadao}', [ProgramaAcaoController::class, 'criarDenuncia'])->name('denunciar.form');
            Route::post('{programa}/denunciar/{cidadao}', [ProgramaAcaoController::class, 'salvarDenuncia'])->name('denunciar.store');
            Route::get('denuncias/historico', [ProgramaAcaoController::class, 'historico'])->name('denunciar.historico');
        });

    // ACOMPANHAMENTOS
    Route::prefix('acompanhamentos')->name('acompanhamentos.')->group(function () {
        Route::get('/', [AcompanhamentoController::class, 'index'])->name('index');
        Route::get('/create', [AcompanhamentoController::class, 'create'])->name('create');



        Route::delete('/{acompanhamento}', [AcompanhamentoController::class, 'destroy'])->name('destroy');
    });



    // EVOLUÇÕES
    Route::prefix('evolucoes')->name('evolucoes.')->group(function () {
        Route::get('/selecionar', [EvolucaoController::class, 'selecionarCidadao'])->name('selecionar');
        Route::get('/iniciar/{cidadao}', [EvolucaoController::class, 'iniciar'])->name('iniciar');

        // coringa por último!
        Route::get('/{acompanhamento}', [EvolucaoController::class, 'index'])->name('index');
        Route::get('/{acompanhamento}/nova', [EvolucaoController::class, 'create'])->name('create');
        Route::post('/{acompanhamento}', [EvolucaoController::class, 'store'])->name('store');
        Route::get('/{acompanhamento}/editar/{evolucao}', [EvolucaoController::class, 'edit'])->name('edit');
        Route::patch('/{acompanhamento}/{evolucao}', [EvolucaoController::class, 'update'])->name('update');
    });

    // Dentro do grupo 'assistente'
    Route::prefix('solicitacoes')->name('solicitacoes.')->group(function () {
        Route::get('/', [SolicitacaoAssistenteController::class, 'index'])->name('index');
        Route::post('/{solicitacao}/responder', [SolicitacaoAssistenteController::class, 'responder'])->name('responder');
        Route::patch('/{solicitacao}/concluir', [SolicitacaoAssistenteController::class, 'concluir'])->name('concluir');
        Route::delete('/{solicitacao}', [SolicitacaoAssistenteController::class, 'destroy'])->name('destroy');
    });

    Route::get('/relatorios', [RelatorioAssistenteController::class, 'index'])->name('relatorios.index');






});


// CIDADÃO – Rotas principais
Route::middleware(['auth', 'role:Cidadao'])->group(function () {
    Route::get('/cidadao/dashboard', [CidadaoController::class, 'dashboard'])->name('cidadao.dashboard');
    Route::get('/editar-perfil', [CidadaoController::class, 'editar'])->name('editar');
    Route::post('/editar-perfil', [CidadaoController::class, 'update'])->name('editar.salvar');

   // Programas
    Route::get('/programas', [ProgramaPublicoController::class, 'index'])->name('cidadao.programas.index');
    Route::get('/programas/{programa}', [ProgramaPublicoController::class, 'ver'])->name('cidadao.programas.ver');

    Route::post('/programas/{programa}/inscrever', [ProgramaPublicoController::class, 'inscrever'])->name('cidadao.programa.inscrever');

    Route::resource('dependentes', DependenteController::class);


});


// CIDADÃO – Perfil Socioeconômico (por etapas)
Route::prefix('cidadao/perfil')->middleware(['auth', 'role:Cidadao'])->name('cidadao.perfil.')->group(function () {
    Route::get('/dados-pessoais', [PerfilController::class, 'dadosPessoais'])->name('dados');
    Route::post('/dados-pessoais', [PerfilController::class, 'salvarDadosPessoais'])->name('dados.salvar');
    Route::get('/moradia', [PerfilController::class, 'moradia'])->name('moradia');
    Route::post('/moradia', [PerfilController::class, 'salvarMoradia'])->name('moradia.salvar');
    Route::get('/composicao-familiar', [PerfilController::class, 'composicao'])->name('composicao');
    Route::post('/composicao-familiar', [PerfilController::class, 'salvarComposicao'])->name('composicao.salvar');
    Route::get('/trabalho', [PerfilController::class, 'trabalho'])->name('trabalho');
    Route::post('/trabalho', [PerfilController::class, 'salvarTrabalho'])->name('trabalho.salvar');
    Route::get('/acessibilidade', [PerfilController::class, 'acessibilidade'])->name('acessibilidade');
    Route::post('/acessibilidade', [PerfilController::class, 'salvarAcessibilidade'])->name('acessibilidade.salvar');
    Route::get('/observacoes', [PerfilController::class, 'observacoes'])->name('observacoes');
    Route::post('/observacoes', [PerfilController::class, 'salvarObservacoes'])->name('observacoes.salvar');



});






Route::get('/cidadao/{id}/ficha', [FichaPublicaController::class, 'mostrar'])->name('cidadao.ficha');

Route::prefix('cidadao')->name('cidadao.')->group(function () {
    // Etapa 1 – Formulário inicial
    Route::get('/recuperar-senha', [RecuperacaoSenhaController::class, 'mostrarFormularioInicial'])->name('recuperar.form');

    // Etapa 2 – Verifica nome + CPF
    Route::post('/recuperar-senha/verificar', [RecuperacaoSenhaController::class, 'verificarNomeCpf'])->name('recuperar.verificar');

    // Etapa 3 – Valida dados extras (nascimento, RG, NIS)
    Route::post('/recuperar-senha/validar', [RecuperacaoSenhaController::class, 'validarDados'])->name('recuperar.validar');

    // Etapa 4 – Salva nova senha
    Route::post('/recuperar-senha/redefinir', [RecuperacaoSenhaController::class, 'atualizarSenha'])->name('recuperar.atualizar');

    // Rotas de fallback para evitar erro em GET (mensagem amigável)
    Route::get('/recuperar-senha/verificar', function () {
        return redirect()->route('cidadao.recuperar.form')
            ->withErrors(['mensagem' => 'Acesso inválido. Preencha o formulário corretamente.']);
    });

    Route::get('/recuperar-senha/validar', function () {
        return redirect()->route('cidadao.recuperar.form')
            ->withErrors(['mensagem' => 'Etapa inválida. Complete os passos anteriores.']);
    });

    Route::get('/recuperar-senha/redefinir', function () {
        return redirect()->route('cidadao.recuperar.form')
            ->withErrors(['mensagem' => 'Essa etapa não pode ser acessada diretamente.']);
    });
});



// Solicitações recebidas pelo Cidadão
Route::prefix('cidadao/solicitacoes')->name('cidadao.solicitacoes.')->middleware(['auth', 'role:Cidadao'])->group(function () {
    Route::get('/', [SolicitacaoCidadaoController::class, 'index'])->name('index');
    Route::get('/{solicitacao}', [SolicitacaoCidadaoController::class, 'show'])->name('show');
    Route::post('/{solicitacao}/responder', [SolicitacaoCidadaoController::class, 'responder'])->name('responder');
    Route::patch('/{solicitacao}/concluir', [SolicitacaoCidadaoController::class, 'concluir'])->name('concluir');
    Route::delete('/{solicitacao}', [SolicitacaoCidadaoController::class, 'destroy'])->name('destroy'); // Só se estiver concluída

});


Route::prefix('cidadao')->middleware(['auth', 'role:Cidadao'])->name('cidadao.')->group(function () {
    Route::get('emergencia', [EmergenciaController::class, 'create'])->name('emergencia.create');
    Route::post('emergencia', [EmergenciaController::class, 'store'])->name('emergencia.store');
    Route::get('emergencia/video/{sala}', [EmergenciaController::class, 'video'])->name('emergencia.video');
    Route::get('emergencia/chamada/{sala}', [EmergenciaController::class, 'chamada'])->name('emergencia.chamada');

    Route::get('relatorios', [CidadaoRelatorioController::class, 'index'])->name('relatorios.index');


});



//RESTAURANTE

Route::middleware(['auth', 'role:Coordenador Restaurante'])
    ->prefix('restaurante/coordenador')
    ->name('restaurante.coordenador.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [CoordenadorDashboardController::class, 'index'])
            ->name('dashboard');

        // CRUD de Restaurantes
        Route::resource('restaurantes', RestauranteController::class);

        // CRUD de Atendentes
        Route::resource('atendentes', AtendenteController::class);

        // Cidadãos normais
        Route::resource('cidadaos', CidadaoRestauranteController::class)
            ->names('cidadaos')
            ->except(['show']);

        // Temporários (com PATCH de renovação antes do resource)
        Route::patch('temporarios/{id}/renovar', [CidadaoTemporarioController::class, 'renovar'])
            ->name('temporarios.renovar'); // sem prefixo duplicado!

        Route::resource('temporarios', CidadaoTemporarioController::class)
            ->names('temporarios')
            ->except(['show']);

        // Vendas
        Route::prefix('vendas')->name('vendas.')->group(function () {
            Route::get('/', [VendaCoordenadorController::class, 'index'])->name('index');
            Route::get('/create', [VendaCoordenadorController::class, 'create'])->name('create');
            Route::post('/', [VendaCoordenadorController::class, 'store'])->name('store');
            Route::post('/finalizar-dia', [VendaCoordenadorController::class, 'finalizarDia'])->name('finalizar-dia');
            Route::delete('/{venda}', [VendaCoordenadorController::class, 'destroy'])->name('destroy');
        });

        // Relatórios
        Route::get('/relatorios', [RelatorioRestauranteController::class, 'index'])
      ->name('relatorios.index');

        Route::get('/relatorios/pdf', [RelatorioRestauranteController::class, 'gerarPdf'])
            ->name('relatorios.pdf');


    });

Route::middleware(['auth', 'role:Atendente Restaurante'])
    ->prefix('restaurante/atendente')
    ->name('restaurante.atendente.')
    ->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

       Route::get('cidadaos/create', [AtendenteCidadaoController::class, 'create'])->name('cidadaos.create');
        Route::post('cidadaos', [AtendenteCidadaoController::class, 'store'])->name('cidadaos.store');

       Route::prefix('vendas')->name('vendas.')->group(function () {
            Route::get('/', [VendaAtendenteController::class, 'index'])->name('index');
            Route::get('/create', [VendaAtendenteController::class, 'create'])->name('create');
            Route::post('/', [VendaAtendenteController::class, 'store'])->name('store');
            Route::post('/finalizar-dia', [VendaAtendenteController::class, 'finalizarDia'])->name('finalizar-dia');
            Route::delete('/{venda}', [VendaAtendenteController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('relatorios')->name('relatorios.')->group(function () {
            Route::get('/', [AtendenteRelatorioController::class, 'index'])->name('index');
            Route::get('/pdf', [AtendenteRelatorioController::class, 'gerarPdf'])->name('pdf');
            Route::get('/excel', [AtendenteRelatorioController::class, 'exportarExcel'])->name('excel');
        });


    });





// Públicos
Route::get('/cidadao/cartao-publico/{id}', [PerfilController::class, 'cartaoPublico'])->name('cidadao.cartao.publico');
Route::get('/cidadao/ficha/{id}', [PerfilPublicoController::class, 'mostrarFicha'])->name('cidadao.ficha.publica');

require __DIR__ . '/auth.php';
