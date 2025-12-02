<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class AssistenteSocialController extends Controller
{
    public function index(Request $request)
    {
        $coordenadorId = Auth::id();

        $query = User::role('Assistente')
            ->where('coordenador_id', $coordenadorId);

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%$busca%")
                  ->orWhere('cpf', 'like', "%$busca%");
            });
        }

        $assistentes = $query->orderBy('name')->paginate(10);

        return view('coordenador.assistentes.index', compact('assistentes'));
    }

    public function create()
{
    if (!Auth::user()->hasRole('Coordenador')) {
        abort(403);
    }

    return view('coordenador.assistentes.create');
}


    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('Coordenador')) {
            abort(403, 'Apenas coordenadores podem criar assistentes.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|size:14|unique:users,cpf',
            'telefone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ], [
            'cpf.unique' => 'CPF já está em uso.',
            'cpf.size' => 'CPF deve estar no formato 000.000.000-00.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $cpf = $this->formatarCpf($request->cpf);
        $telefone = $this->formatarTelefone($request->telefone);

        if (!$this->validarCPF($cpf)) {
            return back()->withErrors(['cpf' => 'CPF inválido.'])->withInput();
        }

        $coordenadorId = Auth::user()->id;

        if (!$coordenadorId) {
            return back()->withErrors(['Erro ao vincular assistente ao coordenador.'])->withInput();
        }

        $assistente = User::create([
            'name' => $request->name,
            'cpf' => $cpf,
            'telefone' => $telefone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'coordenador_id' => $coordenadorId,
        ]);


        $assistente->assignRole('Assistente');

        return redirect()->route('coordenador.assistentes.index')
            ->with('success', 'Assistente cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $assistente = User::findOrFail($id);

        if (!$assistente->hasRole('Assistente') || $assistente->coordenador_id !== Auth::id()) {
            abort(403);
        }

        return view('coordenador.assistentes.edit', compact('assistente'));
    }

    public function update(Request $request, $id)
    {
        $assistente = User::findOrFail($id);

        if (!$assistente->hasRole('Assistente') || $assistente->coordenador_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|size:14|unique:users,cpf,' . $assistente->id,
            'telefone' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:users,email,' . $assistente->id,
            'password' => 'nullable|string|min:6',
        ]);

        $cpf = $this->formatarCpf($request->cpf);
        $telefone = $this->formatarTelefone($request->telefone);

        if (!$this->validarCPF($cpf)) {
            return back()->withErrors(['cpf' => 'CPF inválido.'])->withInput();
        }

        $assistente->update([
            'name' => $request->name,
            'cpf' => $cpf,
            'telefone' => $telefone,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $assistente->password,
        ]);

        return redirect()->route('coordenador.assistentes.index')
            ->with('success', 'Assistente atualizado com sucesso.');
    }

    public function entrar($id)
    {
        $user = User::role('Assistente')
            ->where('id', $id)
            ->where('coordenador_id', Auth::id())
            ->firstOrFail();

        Session::put('impersonate_coordenador_id', Auth::id());

        Auth::login($user);

        return redirect()->route('assistente.dashboard')
            ->with('status', 'Agora você está logado como assistente.');
    }

    private function validarCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }

        return true;
    }

    private function formatarCpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    private function formatarTelefone($telefone)
    {
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        return strlen($telefone) === 11
            ? '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7)
            : $telefone;
    }
}
