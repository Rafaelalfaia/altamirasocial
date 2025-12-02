@extends('layouts.app')

@section('title', 'Painel do Cidadão')

@section('content')
<div class="pt-20 md:pt-0 px-4 space-y-10 max-w-7xl mx-auto">

   {{-- Saudações com foto do cidadão --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-6">

    {{-- Esquerda: Foto e nome --}}
    <div class="flex items-center gap-4">
        @php
            $foto = $cidadao->foto
                ? asset('storage/fotos/' . $cidadao->foto)
                : ($cidadao->user && $cidadao->user->foto
                    ? asset('storage/fotos/' . $cidadao->user->foto)
                    : asset('imagens/avatar-padrao.png'));
        @endphp

        <img src="{{ $foto }}"
             alt="Foto do Cidadão"
             class="w-16 h-16 rounded-full object-cover border-2 border-green-600 shadow">

        <div>
            <h1 class="text-2xl font-bold text-green-800">👋 Bem-vindo, {{ $usuario->name }}</h1>
            <p class="text-sm text-gray-500">Este é seu espaço pessoal no SEMAPS</p>
        </div>

        
    </div>

</div>


    {{-- Indicadores Rápidos --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl shadow text-center">
            <p class="text-sm text-gray-500">Evoluções</p>
            <p class="text-2xl font-bold text-indigo-700">{{ $evolucoesUltimoMes ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow text-center">
            <p class="text-sm text-gray-500">Programas</p>
            <p class="text-2xl font-bold text-green-700">{{ $programas->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow text-center">
            <p class="text-sm text-gray-500">Refeições</p>
            <p class="text-2xl font-bold text-blue-700">{{ $vendasRestaurante ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow text-center">
            <p class="text-sm text-gray-500">Cadastro (%)</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $porcentagemPreenchida ?? 0 }}%</p>
        </div>
    </div>

    {{-- Ações rápidas --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-4">
        <a href="{{ route('cidadao.perfil.dados') }}" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition">
            ✏️ Editar Perfil
        </a>

        <a href="{{ route('cidadao.cartao.publico', $cidadao->id) }}" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition">
            📇 Ver Cartão
        </a>

        <a href="{{ route('cidadao.emergencia.create') }}" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 transition">
            🚨 Emergência
        </a>
    </div>


    {{-- Cartão do Cidadão + Botões --}}
    <div class="flex flex-col lg:flex-row justify-center items-center gap-8 my-8">
        @include('cidadao.cartoes.cartao-frente', ['cidadao' => $cidadao])
        @include('cidadao.cartoes.cartao-verso', ['cidadao' => $cidadao])

        <div class="flex flex-col gap-2 mt-6">
            <button onclick="mostrarQrCode('{{ route('cidadao.cartao.publico', $cidadao->id) }}')" class="text-sm px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700 transition">
                📲 QR Code
            </button>
            <button onclick="baixarPdfCartao('{{ route('cidadao.cartao.publico', $cidadao->id) }}')" class="text-sm px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 transition">
                📄 PDF
            </button>
            <button onclick="compartilharWhatsApp('{{ route('cidadao.cartao.publico', $cidadao->id) }}')" class="text-sm px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition">
                📤 Compartilhar
            </button>
        </div>
    </div>


    {{-- QR Overlay --}}
    <div id="qrOverlay" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden" onclick="fecharQrCode()">
        <div class="bg-white p-4 rounded-lg shadow-lg" onclick="event.stopPropagation()">
            <div id="qrCodeContainer"></div>
            <p class="text-xs text-center text-gray-500 mt-2">Clique fora para fechar</p>
        </div>
    </div>

    {{-- Informações Resumidas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-md border">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">🧍 Dados Pessoais</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li><strong>Nome:</strong> {{ $cidadao->nome }}</li>
                <li><strong>CPF:</strong> {{ $cidadao->cpf }}</li>
                <li><strong>Nascimento:</strong> {{ \Carbon\Carbon::parse($cidadao->data_nascimento)->format('d/m/Y') }}</li>
                <li><strong>Sexo:</strong> {{ $cidadao->sexo }}</li>
                <li><strong>Telefone:</strong> {{ $cidadao->telefone ?? 'Não informado' }}</li>
            </ul>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">🏡 Moradia</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li><strong>Tipo:</strong> {{ $cidadao->tipo_moradia }}</li>
                <li><strong>Água:</strong> {{ $cidadao->tem_agua_encanada ? 'Sim' : 'Não' }}</li>
                <li><strong>Esgoto:</strong> {{ $cidadao->tem_esgoto ? 'Sim' : 'Não' }}</li>
                <li><strong>Energia:</strong> {{ $cidadao->tem_energia ? 'Sim' : 'Não' }}</li>
                <li><strong>Lixo:</strong> {{ $cidadao->tem_coleta_lixo ? 'Sim' : 'Não' }}</li>
            </ul>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">💰 Renda e Família</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li><strong>Renda:</strong> R$ {{ number_format($cidadao->renda_total_familiar, 2, ',', '.') }}</li>
                <li><strong>Pessoas:</strong> {{ $cidadao->pessoas_na_residencia }}</li>
                <li><strong>Ocupação:</strong> {{ $cidadao->ocupacao ?? 'Não informada' }}</li>
            </ul>
        </div>
    </div>

    {{-- Programas Sociais --}}
    <div class="bg-white p-6 rounded-2xl shadow-md border">
        <h2 class="text-xl font-semibold text-indigo-700 mb-4">🎯 Programas Sociais</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($programas as $programa)
                <div class="border border-gray-200 p-4 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800">{{ $programa->titulo }}</h3>
                    <p class="text-sm text-gray-600">{{ $programa->descricao }}</p>
                    <p class="text-xs text-gray-400 mt-2">📅 {{ $programa->created_at->format('d/m/Y') }}</p>
                </div>
            @empty
                <p class="text-gray-500">Nenhum programa disponível no momento.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
    function mostrarQrCode(url) {
        const qr = new QRious({ element: document.createElement('canvas'), value: url, size: 200 });
        const container = document.getElementById('qrCodeContainer');
        container.innerHTML = '';
        container.appendChild(qr.element);
        document.getElementById('qrOverlay').classList.remove('hidden');
    }

    function fecharQrCode() {
        document.getElementById('qrOverlay').classList.add('hidden');
    }

    function compartilharWhatsApp(url) {
        const texto = "Confira meu Cartão do Cidadão 👤: " + url;
        const link = `https://api.whatsapp.com/send?text=${encodeURIComponent(texto)}`;
        window.open(link, '_blank');
    }

    function baixarPdfCartao(url) {
        window.open(url + '?pdf=1', '_blank');
    }
</script>
@endsection
