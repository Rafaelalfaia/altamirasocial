<?php

namespace App\Http\Controllers\Restaurante\Atendente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Cidadao;
use App\Models\User;

class AtendenteCidadaoController extends Controller
{
    public function create()
    {
        return view('restaurante.atendente.cidadaos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|size:14|unique:users,cpf',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        try {
            $cpfLimpo = preg_replace('/\D/', '', $request->cpf);

            $user = User::create([
                'name' => $request->nome,
                'cpf' => $cpfLimpo,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('Cidadao');

            $cidadao = new Cidadao();
            $cidadao->user_id = $user->id;
            $cidadao->cpf = $cpfLimpo;
            $cidadao->nome = $request->nome;

            // Restaurante do atendente logado
            $atendente = Auth::user();
            $restaurante = $atendente->restaurantes()->first();
            if ($restaurante) {
                $cidadao->restaurante_id = $restaurante->id;
            }

            $cidadao->save();

            return redirect()->route('restaurante.atendente.vendas.create')
                ->with('success', '✅ Cidadão criado com sucesso!');
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['erro' => '❌ Erro ao criar cidadão. Verifique os dados e tente novamente.']);
        }
    }
}
