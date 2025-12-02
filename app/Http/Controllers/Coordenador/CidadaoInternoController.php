<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Cidadao;
use App\Models\User;
use App\Models\Bairro;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;



class CidadaoInternoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $cidadaos = Cidadao::with('user')
            ->when($q, fn($w) => $w->where(function($s) use ($q) {
                $s->where('nome', 'like', "%{$q}%")
                  ->orWhere('cpf', 'like', "%{$q}%");
            }))
            ->orderBy('nome')
            ->paginate(15);

        return view('coordenador.cidadaos.index', compact('cidadaos', 'q'));
    }

    public function create()
    {
        return view('coordenador.cidadaos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'      => ['required','string','min:3','max:255'],
            'cpf'       => ['required','regex:/^\d{11}$/','unique:users,cpf','unique:cidadaos,cpf'],
            'email'     => ['nullable','email','max:255','unique:users,email'],
            'password'  => ['required','confirmed','min:6'],
        ], [
            'cpf.regex' => 'Informe o CPF somente com números (11 dígitos).',
        ]);

        $cpf = preg_replace('/\D/', '', $request->cpf);

        DB::transaction(function() use ($request, $cpf) {
            $user = User::create([
                'name'     => $request->nome,
                'cpf'      => $cpf,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('Cidadao');

            Cidadao::create([
                'user_id' => $user->id,
                'nome'    => $request->nome,
                'cpf'     => $cpf,
                'status'  => 'pendente',
            ]);
        });

        return redirect()->route('coordenador.cidadaos.index')->with('success', 'Cidadão criado com sucesso!');
    }


    public function edit($id)
{
    $cidadao = \App\Models\Cidadao::with('user')->findOrFail($id);
    return view('coordenador.cidadaos.edit', compact('cidadao'));
}


   public function update(Request $request, $id)
{
    $cidadao = Cidadao::with('user')->findOrFail($id);
    $user    = $cidadao->user;

    // sanitize ANTES
    $cpfSan = preg_replace('/\D/', '', (string)$request->cpf);
    $telSan = preg_replace('/\D/', '', (string)$request->telefone);
    $request->merge([
        'cpf'      => $cpfSan !== '' ? $cpfSan : null,
        'telefone' => $telSan !== '' ? $telSan : null,
    ]);

    $request->validate([
        'nome'     => ['required','string','min:3','max:255'],
        'cpf'      => [
            'nullable','digits:11',
            \Illuminate\Validation\Rule::unique('users','cpf')->ignore($user->id),
            \Illuminate\Validation\Rule::unique('cidadaos','cpf')->ignore($cidadao->id),
        ],
        'email'    => ['nullable','email','max:255', \Illuminate\Validation\Rule::unique('users','email')->ignore($user->id)],
        'telefone' => ['nullable','regex:/^\d{10,11}$/'],
        'password' => ['nullable','confirmed','min:6'],
        'foto'     => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
    ]);

    // Se não vier com 11 dígitos, zera para NULL (não barra)
    $cpf = ($request->cpf && strlen($request->cpf) === 11) ? $request->cpf : null;

    DB::transaction(function () use ($request, $cidadao, $user, $cpf) {
        if ($request->hasFile('foto')) {
            if (!empty($user->foto) && \Storage::disk('public')->exists($user->foto)) {
                \Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('fotos', 'public');
        }

        $user->name     = $request->nome;
        $user->cpf      = $cpf; // pode ser null
        $user->email    = $request->email;
        $user->telefone = $request->telefone;
        if ($request->filled('password')) {
            $user->password = \Hash::make($request->password);
        }
        $user->save();

        $cidadao->nome = $request->nome;
        $cidadao->cpf  = $cpf; // pode ser null
        $cidadao->save();
    });

    return redirect()->route('coordenador.cidadaos.index')
        ->with('success', 'Dados do cidadão atualizados com sucesso!');
}




     public function destroy($id)
{
    $cidadao = Cidadao::with('user')->findOrFail($id);

    DB::transaction(function () use ($cidadao) {
        // Se usa SoftDeletes nos models, isso manda para a lixeira.
        // Se quiser apagar de vez, troque por forceDelete() (com cuidado).
        if ($cidadao->user) {
            $cidadao->user->syncRoles([]);   // opcional: limpa roles
            $cidadao->user->delete();        // ou ->forceDelete();
        }

        $cidadao->delete();                  // ou ->forceDelete();
    });

    return redirect()
        ->route('coordenador.cidadaos.index')
        ->with('success', 'Cidadão excluído com sucesso.');
}



    public function cartao($id)
    {
        $cidadao = Cidadao::with('bairro.cidade.estado', 'user')->findOrFail($id);
        return view('coordenador.cidadaos.cartao', compact('cidadao'));
    }

    public function entrarComoCidadao($id)
    {
        $cidadao = Cidadao::with('user')->findOrFail($id);
        $usuario = $cidadao->user;

        if (!$usuario) {
            return redirect()->back()->with('error', 'Usuário vinculado não encontrado.');
        }

        session(['impersonate_coordenador_id' => Auth::id()]);
        Auth::login($usuario);

        return redirect()->route('cidadao.dashboard')->with('status', '👤 Você está visualizando como cidadão.');
    }
}
