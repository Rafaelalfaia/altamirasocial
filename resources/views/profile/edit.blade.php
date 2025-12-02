@extends('layouts.app')

@section('title', 'Minha Conta')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Editar Conta</h1>

    @if(session('status'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if(session('senha_alterada'))
        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded mb-4">
            {{ session('senha_alterada') }}
        </div>
    @endif

    @php
        // Máscaras apenas para exibição
        $cpfDigits = preg_replace('/\D/','', old('cpf', $user->cpf ?? ''));
        $cpfMasked = (strlen($cpfDigits) === 11)
            ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfDigits)
            : old('cpf', $user->cpf ?? '');

        $telDigits = preg_replace('/\D/','', old('telefone', $user->telefone ?? ''));
        if (strlen($telDigits) === 11) {
            $telefoneMasked = '('.substr($telDigits,0,2).') '.substr($telDigits,2,5).'-'.substr($telDigits,7);
        } elseif (strlen($telDigits) === 10) {
            $telefoneMasked = '('.substr($telDigits,0,2).') '.substr($telDigits,2,4).'-'.substr($telDigits,6);
        } else {
            $telefoneMasked = old('telefone', $user->telefone ?? '');
        }

        // Avatar fallback -> public/imagens/avatar-padrao.png (com cache-busting se existir)
        $defaultAvatarPath = public_path('imagens/avatar-padrao.png');
        $defaultAvatarUrl  = asset('imagens/avatar-padrao.png') . (file_exists($defaultAvatarPath) ? '?v='.filemtime($defaultAvatarPath) : '');

        $fotoUrl = (!empty($user->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto))
            ? asset('storage/'.$user->foto)
            : $defaultAvatarUrl;
    @endphp


    <form method="POST"
          action="{{ route('profile.update') }}"
          enctype="multipart/form-data"
          class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @method('PATCH') {{-- Breeze usa PATCH para update do profile --}}

        {{-- Foto de Perfil – Somente para não-Cidadão --}}
        @if (!$user->hasAnyRole(['Cidadao','Cidadão']))
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Foto de Perfil</label>
            <div class="flex items-center gap-4 mb-2">
                <img src="{{ $fotoUrl }}" alt="Foto atual"
                     class="w-20 h-20 rounded-full object-cover shadow border border-gray-200">
                <input type="file" name="foto" id="foto"
                       class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm">
            </div>
            @error('foto')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>
        @endif

        {{-- Nome --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
            @error('name')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- CPF --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">CPF</label>
            <input type="text" name="cpf" id="cpf" maxlength="14" value="{{ $cpfMasked }}"
                   oninput="formatarCPF(this)" placeholder="000.000.000-00" required
                   class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-zinc-600">
            @error('cpf')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- E-mail --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">E-mail (opcional)</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
            @error('email')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Telefone --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Telefone</label>
            <input type="text" name="telefone" id="telefone" value="{{ $telefoneMasked }}"
                   oninput="formatarTelefone(this)" placeholder="(00) 00000-0000"
                   class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 text-zinc-600">
            @error('telefone')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Nova Senha --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Nova Senha</label>
            <input type="password" name="password" id="nova_senha"
                   class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
            @error('password')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Confirmar Nova Senha --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
            <input type="password" name="password_confirmation"
                   class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
        </div>

        {{-- Botão --}}
        <div class="md:col-span-2 flex justify-end">
            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white font-semibold px-4 py-2 rounded shadow">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>

{{-- JS: apenas máscaras visuais --}}
<script>
    function formatarCPF(campo) {
        let v = campo.value.replace(/\D/g, '');
        if (v.length > 11) v = v.slice(0, 11);
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        campo.value = v;
    }
    function formatarTelefone(campo) {
        let v = campo.value.replace(/\D/g, '');
        if (v.length > 11) v = v.slice(0, 11);
        v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
        v = v.replace(/(\d{5})(\d{4})$/, '$1-$2');
        campo.value = v;
    }
</script>
@endsection
