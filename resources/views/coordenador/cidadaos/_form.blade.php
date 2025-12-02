@php
    /** @var \App\Models\Cidadao|null $cidadao */
    $isEdit = isset($cidadao);
    $u = $cidadao->user ?? null;
@endphp

<div class="grid grid-cols-1 gap-4">
    {{-- Nome --}}
    <div>
        <label class="block text-sm font-medium mb-1">Nome</label>
        <input type="text" name="nome" required
               value="{{ old('nome', $cidadao->nome ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    {{-- CPF --}}
    <div>
        <label class="block text-sm font-medium mb-1">CPF</label>
        <input type="text" name="cpf" required
               placeholder="Somente números"
               value="{{ old('cpf', $cidadao->cpf ?? '') }}"
               class="w-full border rounded px-3 py-2">
        <p class="text-xs text-gray-500 mt-1">Digite apenas números (11 dígitos).</p>
    </div>

    {{-- E-mail (opcional) --}}
    <div>
        <label class="block text-sm font-medium mb-1">E-mail (opcional)</label>
        <input type="email" name="email"
               value="{{ old('email', $u->email ?? '') }}"
               class="w-full border rounded px-3 py-2">
    </div>

    {{-- Senha / Confirmar --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">
                Senha {{ $isEdit ? '(deixe em branco para manter)' : '' }}
            </label>
            <input type="password" name="password" {{ $isEdit ? '' : 'required' }}
                   class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Confirmar Senha</label>
            <input type="password" name="password_confirmation" {{ $isEdit ? '' : 'required' }}
                   class="w-full border rounded px-3 py-2">
        </div>
    </div>
</div>
