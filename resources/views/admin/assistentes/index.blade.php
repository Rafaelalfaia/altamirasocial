@extends('layouts.app')

@section('title', 'Assistentes Cadastrados')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-6 space-y-6">

    {{-- Título --}}
    <h1 class="text-2xl font-bold text-green-900 flex items-center gap-2">
        🤝 Assistentes Cadastrados
    </h1>

    {{-- Filtro --}}
    <form method="GET" class="mb-4">
        <input type="text" name="busca" placeholder="Buscar por nome ou e-mail"
               value="{{ $busca }}"
               class="border-gray-300 rounded shadow-sm w-full md:w-1/3 text-sm px-3 py-2"
        />
    </form>

    {{-- Tabela --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-green-800 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Nome</th>
                    <th class="px-4 py-2 text-left">E-mail</th>
                    <th class="px-4 py-2 text-left">Funções</th>
                    <th class="px-4 py-2 text-left">Coordenadores</th>
                    <th class="px-4 py-2 text-left">Criado em</th>
                    <th class="px-4 py-2 text-left">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($assistentes as $assistente)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $assistente->name }}</td>
                        <td class="px-4 py-2">{{ $assistente->email }}</td>

                        {{-- Roles --}}
                        <td class="px-4 py-2">
                            @foreach($assistente->getRoleNames() as $role)
                                <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded mr-1">
                                    {{ $role }}
                                </span>
                            @endforeach
                        </td>

                        {{-- Coordenadores --}}
                        <td class="px-4 py-2">
                            @if($assistente->coordenadores && $assistente->coordenadores->count())
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($assistente->coordenadores as $coord)
                                        <li class="text-gray-700">{{ $coord->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Data --}}
                        <td class="px-4 py-2">{{ $assistente->created_at->format('d/m/Y') }}</td>

                        {{-- Ações --}}
                        <td class="px-4 py-2 space-x-2">
                            {{-- Editar --}}
                            <a href="{{ route('admin.assistentes.edit', $assistente->id) }}"
                               class="text-blue-600 hover:underline text-sm">✏️ Editar</a>

                            {{-- Entrar (impersonar) --}}
                            @if(!session()->has('impersonate_admin_id'))
                            <a href="{{ route('admin.assistentes.entrar', $assistente->id) }}"
                                   class="text-green-700 hover:underline text-sm">🔑 Entrar</a>
                            @else
                                <span class="text-gray-400 text-xs italic">em modo acesso</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div class="mt-6">
        {{ $assistentes->withQueryString()->links() }}
    </div>
</div>
@endsection
