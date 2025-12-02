<div class="bg-white rounded-xl shadow p-4 text-center">
    <h2 class="text-lg font-bold text-indigo-600">Cartão do Cidadão</h2>

    @if ($cidadao->foto)
        <img src="{{ asset('storage/fotos/' . $cidadao->foto) }}"
            class="w-20 h-20 rounded-full mx-auto mt-2 object-cover border-2 border-indigo-500" alt="Foto do cidadão">
    @endif

    <p class="mt-2 font-semibold">{{ $cidadao->nome }}</p>

    <a href="{{ route('cidadao.cartao.publico', $cidadao->id) }}"
        class="mt-3 inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
        Ver Cartão Completo
    </a>
</div>