<?php

namespace App\Http\Controllers\Cidadao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cidadao;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RecuperacaoSenhaController extends Controller
{
    /**
     * Etapa 1 – Exibe o formulário com Nome + CPF
     */
    public function mostrarFormularioInicial()
    {
        return view('cidadao.auth.recuperar');
    }

    /**
     * Etapa 2 – Verifica nome e CPF e mostra campos extras (dinâmicos)
     */
    public function verificarNomeCpf(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'cpf' => 'required|string',
        ]);

        $nomeInput = strtolower(trim($request->nome));
        $cpfLimpo = preg_replace('/\D/', '', $request->cpf);

        $cidadao = Cidadao::where('cpf', $cpfLimpo)
            ->whereRaw('LOWER(nome) LIKE ?', ["%{$nomeInput}%"])
            ->first();

        if (!$cidadao || !$cidadao->user) {
            return back()->withErrors(['cpf' => 'Nome ou CPF não encontrados ou não vinculados a um usuário.']);
        }

        return view('cidadao.auth.recuperar-verificar', compact('cidadao'));
    }

    /**
     * Etapa 3 – Valida campos extras (nascimento, RG, NIS) e libera redefinição
     */
    public function validarDados(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|exists:cidadaos,cpf',
        ]);

        $cpf = preg_replace('/\D/', '', $request->cpf);
        $cidadao = Cidadao::where('cpf', $cpf)->first();

        if (!$cidadao || !$cidadao->user) {
            return back()->withErrors(['cpf' => 'CPF inválido ou sem vínculo com usuário.']);
        }

        // Verificações condicionais com limpeza
        if ($cidadao->data_nascimento && trim($request->data_nascimento) !== $cidadao->data_nascimento) {
            return back()->withErrors(['data_nascimento' => 'Data de nascimento incorreta.']);
        }

        if ($cidadao->rg && trim($request->rg) !== $cidadao->rg) {
            return back()->withErrors(['rg' => 'RG incorreto.']);
        }

        if ($cidadao->nis && trim($request->nis) !== $cidadao->nis) {
            return back()->withErrors(['nis' => 'NIS incorreto.']);
        }

        return view('cidadao.auth.recuperar-redefinir', ['user' => $cidadao->user]);
    }

    /**
     * Etapa 4 – Atualiza a senha no banco
     */
    public function atualizarSenha(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();

        // (opcional) Logout de outras sessões
        // auth()->logoutOtherDevices($request->password);

        return redirect()->route('login')->with('status', 'Senha redefinida com sucesso! Faça login.');
    }
}
