<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cidadao;
use App\Models\Bairro;

class UsuarioController extends Controller
{

    public function index(Request $request)
    {
        $bairroId = $request->input('bairro_id');
        $nome     = trim((string) $request->query('q', $request->query('nome', '')));
        $cpfInput = preg_replace('/\D+/', '', (string) $request->query('cpf', ''));

        $bairros = Bairro::with('cidade')->orderBy('nome')->get();

        $cidadaos = Cidadao::with(['user', 'bairro.cidade'])
            ->when($bairroId, fn($w) => $w->where('bairro_id', $bairroId))
            ->when($nome !== '' || $cpfInput !== '', function ($w) use ($nome, $cpfInput) {
                $w->where(function ($s) use ($nome, $cpfInput) {
                    if ($nome !== '') {
                        $s->where('nome', 'like', "%{$nome}%");
                    }
                    $s->orWhere('cpf', 'like', '%' . ($cpfInput !== '' ? $cpfInput : $nome) . '%');
                });
            })
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return view('assistente.usuarios.index', compact('cidadaos', 'bairros', 'bairroId'));
    }


    public function show($id)
    {
        $usuario = User::with('roles')->findOrFail($id);
        return view('assistente.usuarios.show', compact('usuario'));
    }


    public function editar($id)
    {
        $cidadao = Cidadao::with(['user', 'bairro.cidade.estado'])
            ->where('user_id', $id)
            ->firstOrFail();

        return view('cidadao.perfil.dados', compact('cidadao'));
    }
}
