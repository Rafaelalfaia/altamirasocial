@extends('layouts.app')

@section('title', 'Criar Programa')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Novo Programa</h1>
            <p class="text-sm text-gray-600">Cadastre um novo programa social e defina as regiões atendidas.</p>
        </div>

        {{-- Corrigido: rota com prefixo "coordenador." --}}
        <a href="{{ route('coordenador.programas.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            ← Voltar
        </a>
    </div>

    {{-- Alertas de validação --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="font-semibold text-red-700 mb-1">Erros no formulário</div>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card do formulário --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
        {{-- Corrigido: rota com prefixo "coordenador." + enctype p/ upload --}}
        <form action="{{ route('coordenador.programas.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            {{-- Seção: Dados básicos --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="nome" class="block text-sm font-semibold text-gray-800">Nome do Programa <span class="text-red-500">*</span></label>
                    <input type="text" id="nome" name="nome" value="{{ old('nome') }}"
                           class="mt-1 block w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Ex.: Cartão Solidário, Restaurante Popular, etc." required>
                    @error('nome') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="descricao" class="block text-sm font-semibold text-gray-800">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"
                              class="mt-1 block w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                              placeholder="Descreva o objetivo, público-alvo e critérios gerais...">{{ old('descricao') }}</textarea>
                    @error('descricao') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="publico_alvo" class="block text-sm font-semibold text-gray-800">Público-alvo</label>
                    <input type="text" id="publico_alvo" name="publico_alvo" value="{{ old('publico_alvo') }}"
                           class="mt-1 block w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Ex.: Famílias em vulnerabilidade">
                    @error('publico_alvo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="vagas" class="block text-sm font-semibold text-gray-800">Vagas <span class="text-red-500">*</span></label>
                    <input type="number" min="1" id="vagas" name="vagas" value="{{ old('vagas') }}"
                           class="mt-1 block w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ex.: 100" required>
                    @error('vagas') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="valor_corte" class="block text-sm font-semibold text-gray-800">Valor de corte (R$)</label>
                    <input type="number" step="0.01" min="0" id="valor_corte" name="valor_corte" value="{{ old('valor_corte') }}"
                           class="mt-1 block w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ex.: 0,00">
                    @error('valor_corte') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-800">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status"
                            class="mt-1 block w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                        @php $statusAtual = old('status', 'ativado'); @endphp
                        <option value="ativado" {{ $statusAtual === 'ativado' ? 'selected' : '' }}>Ativado</option>
                        <option value="desativado" {{ $statusAtual === 'desativado' ? 'selected' : '' }}>Desativado</option>
                    </select>
                    @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Seção: Regras / Público-alvo --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Aceita menores (toggle sem Alpine) --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-800">Aceita menores de idade?</label>
                    <div class="mt-2 flex items-center gap-3">
                        <input type="checkbox" id="aceita_menores" name="aceita_menores" value="1"
                               class="sr-only peer" {{ old('aceita_menores') ? 'checked' : '' }}>
                        <label for="aceita_menores"
                               class="relative inline-flex h-7 w-12 cursor-pointer items-center rounded-full bg-gray-200 transition peer-checked:bg-emerald-600">
                            <span class="absolute left-1 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                            <span class="sr-only">Aceita menores</span>
                        </label>
                        <span class="text-sm text-gray-700">
                            {{ old('aceita_menores') ? 'Sim, aceita' : 'Não aceita' }}
                        </span>
                    </div>
                    @error('aceita_menores') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Regiões atendidas (padronizadas) --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-800">Regiões Atendidas <span class="text-red-500">*</span></label>
                    <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3">
                        @php
                            $REGIOES = ['Altamira', 'Castelo dos Sonhos e Cachoeira da Serra'];
                            $oldRegioes = old('regioes', []);
                        @endphp

                        @foreach($REGIOES as $r)
                            @php $id = 'regiao_'.\Illuminate\Support\Str::slug($r); @endphp
                            <label for="{{ $id }}" class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" id="{{ $id }}" name="regioes[]"
                                       value="{{ $r }}"
                                       class="mt-1 h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                       {{ in_array($r, $oldRegioes) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-800">{{ $r }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('regioes')   <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    @error('regioes.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            <hr class="border-gray-100">

            {{-- Seção: Imagens --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="foto_perfil" class="block text-sm font-semibold text-gray-800">Foto de perfil (JPG/PNG até 2MB)</label>
                    <input type="file" id="foto_perfil" name="foto_perfil"
                           accept=".jpg,.jpeg,.png"
                           class="mt-1 block w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('foto_perfil') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="foto_capa" class="block text-sm font-semibold text-gray-800">Foto de capa (JPG/PNG até 4MB)</label>
                    <input type="file" id="foto_capa" name="foto_capa"
                           accept=".jpg,.jpeg,.png"
                           class="mt-1 block w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('foto_capa') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Ações --}}
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('coordenador.programas.index') }}"
                   class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none">
                    Salvar Programa
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 text-xs text-gray-500">
        Observação: as regiões válidas estão padronizadas como <strong>“Altamira”</strong> e
        <strong>“Castelo dos Sonhos e Cachoeira da Serra”</strong>. Garanta que o backend valide exatamente esses valores.
    </div>
</div>
@endsection
