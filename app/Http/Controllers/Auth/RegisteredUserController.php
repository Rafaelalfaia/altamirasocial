<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // normaliza e decide modo
        $loginType = $request->input('login_type', 'cpf'); // 'cpf' | 'email'
        $rawCpf    = (string) ($request->input('cpf') ?? '');
        $cpf       = preg_replace('/\D+/', '', $rawCpf); // só dígitos
        $email     = trim((string) $request->input('email'));

        // reatribui normalizados (para mensagens coerentes)
        $request->merge([
            'cpf'   => $cpf,
            'email' => $email !== '' ? strtolower($email) : null,
        ]);

        // regras condicionais
        $rulesBase = [
            'name'       => ['required', 'string', 'max:255'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'login_type' => ['required', Rule::in(['cpf', 'email'])],
        ];

        if ($loginType === 'cpf') {
            $rulesExtra = [
                'cpf'   => ['required', 'digits:11', 'unique:users,cpf'],
                'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            ];
        } else { // login_type === 'email'
            $rulesExtra = [
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'cpf'   => ['nullable', 'digits:11', 'unique:users,cpf'],
            ];
        }

        $messages = [
            'cpf.required'    => 'O campo CPF é obrigatório quando você escolhe registrar por CPF.',
            'cpf.digits'      => 'O CPF deve conter exatamente 11 dígitos (sem pontos ou traços).',
            'cpf.unique'      => 'Este CPF já está em uso.',
            'email.required'  => 'O e-mail é obrigatório quando você escolhe registrar por e-mail.',
            'email.email'     => 'Informe um e-mail válido.',
            'email.unique'    => 'Este e-mail já está em uso.',
            'password.confirmed' => 'As senhas não coincidem.',
        ];

        $request->validate($rulesBase + $rulesExtra, $messages);

        // garante null quando vazio
        $cpf   = ($cpf !== '') ? $cpf : null;
        $email = ($email !== '') ? strtolower($email) : null;

        // cria o usuário
        $user = User::create([
            'name'     => $request->name,
            'cpf'      => $cpf,
            'email'    => $email,
            'password' => Hash::make($request->password),
        ]);

        // papel padrão
        $user->assignRole('Cidadao');

        // login + redirect
        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
