<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Cidadao;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $usuario = Auth::user();

        // Busca o cidadão com relações
        $cidadao = Cidadao::with('bairro.cidade.estado', 'user')
            ->where('user_id', $usuario->id)
            ->first();

        if (!$cidadao) {
            abort(404, 'Cidadão não encontrado.');
        }

        // Foto do cidadão (com fallback)
        $fotoCaminho = $cidadao->foto
            ? asset('storage/fotos/' . $cidadao->foto)
            : ($cidadao->user && $cidadao->user->foto ? asset('storage/fotos/' . $cidadao->user->foto) : asset('imagens/avatar-padrao.png'));

        // Lista de campos obrigatórios
        $camposObrigatorios = [
            'bairro_id',
            'nome',
            'cpf',
            'data_nascimento',
            'sexo',
            'telefone',
            'cep',
            'rua',
            'numero',
            'tipo_moradia',
            'tem_esgoto',
            'tem_agua_encanada',
            'tem_coleta_lixo',
            'tem_energia',
            'renda_total_familiar',
            'pessoas_na_residencia',
            'ocupacao',
            'grau_parentesco',
            'escolaridade',
            'pcd',
            'cor_raca',
            'nis',
            'rg',
            'orgao_emissor',
            'data_emissao_rg',
            'titulo_eleitor',
            'zona',
            'secao',
        ];

        // Cálculo de preenchimento
        $totalCampos = count($camposObrigatorios);
        $preenchidos = collect($camposObrigatorios)->filter(function ($campo) use ($cidadao) {
            return !is_null($cidadao->$campo) && $cidadao->$campo !== '';
        })->count();

        $preenchimento = $totalCampos > 0 ? $preenchidos / $totalCampos : 0;
        $cadastroCompleto = $preenchimento === 1.0;

        // QR Code da ficha pública
        $qrCodeSvg = null;
        try {
            if (app('router')->has('cidadao.ficha.publica')) {
                $urlFicha = route('cidadao.ficha.publica', $cidadao->id);
                $qrCodeSvg = QrCode::size(120)->generate($urlFicha);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return view('cidadao.dashboard', compact(
            'usuario',
            'cidadao',
            'qrCodeSvg',
            'cadastroCompleto',
            'preenchimento',
            'fotoCaminho'
        ));
    }
}
