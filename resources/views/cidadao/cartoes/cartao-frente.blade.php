<div
    class="relative w-full max-w-[360px] aspect-[7/4] bg-white rounded-xl shadow-lg overflow-hidden border border-gray-300">

    {{-- Fundo do cartão --}}
    <img src="{{ asset('imagens/fundo-frente.png') }}" alt="Fundo Frente"
         class="absolute inset-0 w-full h-full object-cover">

    {{-- Conteúdo sobreposto --}}
    <div class="absolute inset-0 z-10 flex items-center px-4 py-3 text-white text-sm">
        {{-- Coluna da foto --}}
            <div class="flex-shrink-0">
                @if ($cidadao->foto)
                    <img src="{{ asset('storage/fotos/' . $cidadao->foto) }}"
                        class="w-24 h-24 rounded-full object-cover border-2 border-white shadow-lg"
                        alt="Foto do Cidadão">
                @else
                    <img src="{{ asset('imagens/avatar-padrao.png') }}"
                        class="w-24 h-24 rounded-full object-cover border-2 border-white shadow-lg"
                        alt="Foto padrão">
                @endif
            </div>


        {{-- Coluna das informações --}}
        <div class="ml-4 space-y-1 leading-tight w-full text-left">
            {{-- Nome alinhado à esquerda --}}
            <p class="font-bold text-base text-left">{{ $cidadao->nome }}</p>

            {{-- Endereço --}}
            @php
                $endereco = collect([
                    $cidadao->rua,
                    $cidadao->numero,
                    data_get($cidadao, 'bairro.nome'),
                    data_get($cidadao, 'bairro.cidade.nome'),
                    data_get($cidadao, 'bairro.cidade.estado.nome'),
                ])->filter()->implode(', ');
            @endphp
            <p class="text-xs flex items-center gap-1">
                <span>📍</span> <span>{{ $endereco }}</span>
            </p>

            {{-- Telefone --}}
            <p class="text-xs flex items-center gap-1">
                <span>📱</span> <span>{{ $cidadao->telefone ?? 'Não informado' }}</span>
            </p>

            {{-- Nascimento --}}
            <p class="text-xs flex items-center gap-1">
                <span>🗓️</span>
                <span>
                    {{ $cidadao->data_nascimento
                        ? \Carbon\Carbon::parse($cidadao->data_nascimento)->format('d/m/Y')
                        : 'Data não informada' }}
                </span>
            </p>
        </div>
    </div>
</div>
