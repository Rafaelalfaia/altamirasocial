<?php

namespace App\Http\Controllers;

use App\Models\Cidadao;
use App\Rules\CpfValido;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Exibe o formulário de edição de perfil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Atualiza os dados do perfil.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Sanitiza CPF e telefone (mantemos apenas dígitos)
        $cpfSan = preg_replace('/\D/', '', (string) $request->input('cpf'));
        $telSan = preg_replace('/\D/', '', (string) $request->input('telefone'));

        $request->merge([
            'cpf'      => $cpfSan,
            'telefone' => $telSan,
        ]);

        // Regras de validação
        $rules = [
            'name'      => ['required', 'string', 'max:255'],
            'cpf'       => ['required', 'string', 'size:11', new CpfValido, Rule::unique('users', 'cpf')->ignore($user->id)],
            'email'     => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'telefone'  => ['nullable', 'regex:/^\d{10,11}$/'], // 10 (fixo) ou 11 (cel)
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        // Permitir foto apenas para quem NÃO for cidadão (considera variações com/sem acento)
        if (!$user->hasAnyRole(['Cidadao', 'Cidadão'])) {
            $rules['foto'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }

        $validated = $request->validate($rules, [
            'cpf.unique'      => 'CPF já cadastrado por outro usuário.',
            'cpf.size'        => 'CPF deve ter 11 dígitos.',
            'telefone.regex'  => 'Telefone deve conter 10 ou 11 dígitos (somente números).',
            'foto.image'      => 'O arquivo deve ser uma imagem.',
            'foto.max'        => 'O tamanho máximo da imagem é 2MB.',
        ]);

        // Atualiza dados básicos do User
        $user->name     = $validated['name'];
        $user->cpf      = $validated['cpf'];
        $user->email    = $validated['email'] ?? null;
        $user->telefone = !empty($validated['telefone']) ? $validated['telefone'] : null;

        // Atualiza foto se permitido
        if ($request->hasFile('foto') && !$user->hasAnyRole(['Cidadao', 'Cidadão'])) {
            // apaga antiga se existir
            if (!empty($user->foto) && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            // salva nova e grava o CAMINHO (ex.: 'fotos/abc123.webp')
            $path = $request->file('foto')->store('fotos', 'public');
            $user->foto = $path;
        }

        // Atualiza senha, se informada (com HASH!)
        $senhaAlterada = false;
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
            $senhaAlterada = true;
        }

        // Persiste User
        $user->save();

        // Sincroniza nome/CPF no Cidadao (se houver vínculo)
        $cidadao = Cidadao::where('user_id', $user->id)->first();
        if ($cidadao) {
            $cidadao->nome = $user->name;
            $cidadao->cpf  = $user->cpf;
            $cidadao->save();
        }

        return redirect()->route('profile.edit')->with([
            'status'          => 'Perfil atualizado com sucesso.',
            'senha_alterada'  => $senhaAlterada ? 'Senha alterada com sucesso!' : null,
        ]);
    }

    /**
     * Remove a conta do usuário.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
