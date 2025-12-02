<?php

namespace App\Http\Controllers\Assistente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cidadao;
use App\Models\Bairro;
use App\Models\User;
use App\Models\Programa;
use App\Models\Inscricao;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProgramaAssistenteController extends Controller
{
    public function index()
    {
        $programas = Programa::latest()->paginate(10);
        return view('assistente.programas.index', compact('programas'));
    }
    public function criar()
    {
        return view('assistente.cidadao.criar');
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'cpf' => ['required', 'string', 'size:14'],
            'password' => ['required', 'confirmed', 'min:6'],
            'email' => ['nullable', 'email'],
        ]);

        $cpfLimpo = preg_replace('/\D/', '', $request->cpf);

        $existe = User::where('cpf', $cpfLimpo)->first();
        if ($existe) {
            return back()->withErrors(['cpf' => 'CPF cadastrado por outro usuário.'])->withInput();
        }

        $user = new User();
        $user->name = 'Novo Cidadão';
        $user->cpf = $cpfLimpo;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $user->assignRole('Cidadao');

        $cidadao = new Cidadao();
        $cidadao->user_id = $user->id;
        $cidadao->nome = 'Novo Cidadão';
        $cidadao->cpf = $cpfLimpo;
        $cidadao->save();

        return redirect()->route('assistente.cidadao.dados.editar', $cidadao->id)
            ->with('success', 'Cidadão criado com sucesso!');
    }

    public function destroy($id)
    {
        $cidadao = Cidadao::findOrFail($id);
        $user = $cidadao->user;

        if ($cidadao->foto) {
            Storage::disk('public')->delete($cidadao->foto);
        }

        $cidadao->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('assistente.usuarios.index')
            ->with('success', 'Cidadão excluído com sucesso!');
    }

    public function inscritosAprovados($id)
    {
        $programa = Programa::findOrFail($id);
        $inscricoes = Inscricao::with('cidadao')
            ->where('programa_id', $id)
            ->where('status', 'aprovado')
            ->get();

        return view('assistente.programas.inscritos_aprovados', compact('programa', 'inscricoes'));
    }

    public function inscritosPendentes($id)
    {
        $programa = Programa::findOrFail($id);
        $inscricoes = Inscricao::with('cidadao')
            ->where('programa_id', $id)
            ->where('status', 'pendente')
            ->get();

        return view('assistente.programas.inscritos_pendentes', compact('programa', 'inscricoes'));
    }

    public function inscritosReprovados($id)
    {
        $programa = Programa::findOrFail($id);
        $inscricoes = Inscricao::with('cidadao')
            ->where('programa_id', $id)
            ->where('status', 'reprovado')
            ->get();

        return view('assistente.programas.inscritos_reprovados', compact('programa', 'inscricoes'));
    }

    public function atualizarStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aprovado,pendente,reprovado'
        ]);

        $inscricao = Inscricao::findOrFail($id);
        $inscricao->status = $request->status;
        $inscricao->save();

        return back()->with('success', 'Status da inscrição atualizado com sucesso.');
    }

    // Demais métodos permanecem inalterados...
}
