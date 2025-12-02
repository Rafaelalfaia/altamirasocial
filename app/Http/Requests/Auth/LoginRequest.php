<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;


class LoginRequest extends FormRequest
{
    /**
     * Autorização da requisição
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Autentica o usuário usando CPF ou e-mail
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginOriginal = $this->input('login');

        // Detecta se é e-mail ou CPF
        $isEmail = filter_var($loginOriginal, FILTER_VALIDATE_EMAIL);
        $field = $isEmail ? 'email' : 'cpf';

        // Limpa o CPF (remove máscara)
        $login = $isEmail ? $loginOriginal : preg_replace('/\D/', '', $loginOriginal);

        // Tenta autenticar
        $credentials = [
            $field => $login,
            'password' => $this->input('password'),
        ];

        $remember = $this->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Essas credenciais não correspondem aos nossos registros.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());



    }

    /**
     * Garante que o login não foi bloqueado por muitas tentativas
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Gera uma chave única para controle de tentativas
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')) . '|' . $this->ip());
    }
}
