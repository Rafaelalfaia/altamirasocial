<?php

namespace App\Http\Controllers\Restaurante\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Cidadao;
use App\Models\User;
use App\Models\Bairro;

class CidadaoRestauranteController extends Controller
{
    public function index(Request $request)
    {
        $busca = $request->input('busca');

        $cidadaos = Cidadao::with('user')
            ->when($busca, function ($query, $busca) {
                $query->where('nome', 'like', "%{$busca}%")
                    ->orWhere('cpf', 'like', "%{$busca}%")
                    ->orWhereHas('user', function ($q) use ($busca) {
                        $q->where('email', 'like', "%{$busca}%")
                            ->orWhere('name', 'like', "%{$busca}%");
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('restaurante.coordenador.cidadaos.index', compact('cidadaos', 'busca'));
    }


    public function create()
    {
        $bairros = Bairro::with('cidade')->get();
        return view('restaurante.coordenador.cidadaos.create', compact('bairros'));
    }

   public function store(Request $request)
    {
        $cpfLimpo = preg_replace('/\D/', '', $request->cpf); // Limpa o CPF para 11 dígitos puros

        $request->merge(['cpf' => $cpfLimpo]); // Usa o CPF limpo na validação

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => ['required', 'digits:11', Rule::unique('users', 'cpf')],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:6',
        ]);

        try {
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

            $coordenador = Auth::user();
            $primeiroRestaurante = $coordenador->restaurantes->first();
            if ($primeiroRestaurante) {
                $cidadao->restaurante_id = $primeiroRestaurante->id;
            }

            $cidadao->save();

            return redirect()->route('restaurante.coordenador.cidadaos.index')
                ->with('success', '✅ Cidadão criado com sucesso!');
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['erro' => '❌ Erro ao criar cidadão. Verifique os dados e tente novamente.']);
        }
    }



    public function edit($id)
    {
        $cidadao = Cidadao::with('user')->findOrFail($id);
        $bairros = Bairro::with('cidade')->get();

        return view('restaurante.coordenador.cidadaos.edit', compact('cidadao', 'bairros'));
    }

   public function update(Request $request, $id)
    {
        $cidadao = Cidadao::with('user')->findOrFail($id);
        $user = $cidadao->user;

        $cpfLimpo = preg_replace('/\D/', '', $request->cpf); // LIMPA ANTES DE VALIDAR

        $request->merge(['cpf' => $cpfLimpo]); // substitui o valor com o cpf limpo (11 dígitos)

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => [
                'required',
                'digits:11',
                Rule::unique('users', 'cpf')->ignore($user->id),
            ],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', 'min:6'],
        ]);

        $user->update([
            'cpf' => $cpfLimpo,
            'email' => $request->email,
            'name' => $request->nome,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        $cidadao->nome = $request->nome;
        $cidadao->cpf = $cpfLimpo;
        $cidadao->save();

        return redirect()->route('restaurante.coordenador.cidadaos.index')
            ->with('success', '✅ Cidadão atualizado com sucesso.');
    }



    public function destroy($id)
    {
        $cidadao = Cidadao::with('user')->findOrFail($id);
        $user = $cidadao->user;

        $cidadao->delete();
        if ($user) $user->delete();

        return redirect()->route('restaurante.coordenador.cidadaos.index')->with('success', '🗑️ Cidadão excluído com sucesso.');
    }
}
