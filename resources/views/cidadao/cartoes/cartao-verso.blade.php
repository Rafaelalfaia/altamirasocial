<div
    class="relative w-full max-w-[360px] aspect-[7/4] bg-white rounded-xl shadow-lg overflow-hidden border border-gray-300">

    {{-- Fundo do cartão --}}
    <img src="{{ asset('imagens/fundo-verso.png') }}" alt="Fundo Verso" class="absolute w-full h-full object-cover">

   
    {{-- Área central com QR Code e validação --}}
    <div class="relative z-10 flex flex-col items-center justify-center h-full px-4">

        {{-- QR Code apontando para a ficha pública --}}
        <div class="w-24 h-24 bg-white rounded-md shadow flex items-center justify-center mb-3">
            {!! QrCode::size(90)->generate(route('cidadao.ficha.publica', $cidadao->id)) !!}
        </div>

        {{-- Cálculo do código de validação --}}
        @php
            $cpf = preg_replace('/[^0-9]/', '', $cidadao->cpf ?? '');
            $rg = preg_replace('/[^0-9]/', '', $cidadao->rg ?? '');
            $cpf_fim = str_pad(substr($cpf, -3), 3, '0', STR_PAD_LEFT);
            $rg_inicio = str_pad(substr($rg, 0, 3), 3, '0', STR_PAD_LEFT);
            $codigo_rg = is_numeric($rg_inicio) ? str_pad((int) $rg_inicio * 3, 3, '0', STR_PAD_LEFT) : '000';
        @endphp

        {{-- Texto do código de validação --}}
        <p class="text-white text-xs mt-2">Código de validação:</p>
        <p class="text-white text-sm font-bold tracking-widest">{{ $cpf_fim }}{{ $codigo_rg }}</p>
    </div>
</div>