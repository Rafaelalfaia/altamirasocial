@extends('layouts.app')
@section('title','Relatórios de Programas')

@section('content')
<div class="max-w-6xl mx-auto p-4 md:p-6">

  <div class="flex items-center justify-between mb-3">
    <h1 class="text-xl font-bold">Relatórios (últimos {{ $dias }} dias)</h1>
    <form method="GET" class="flex items-center gap-2">
      <select name="periodo" class="border rounded-lg px-3 py-2">
        <option value="7d"  @selected($periodo==='7d')>7 dias</option>
        <option value="30d" @selected($periodo==='30d')>30 dias</option>
        <option value="90d" @selected($periodo==='90d')>90 dias</option>
      </select>
      <button class="bg-gray-900 text-white px-3 py-2 rounded-lg">Aplicar</button>
    </form>
  </div>

  <div class="bg-white rounded-xl shadow border p-4">
    <h3 class="font-semibold mb-2">Inscrições por Programa</h3>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left">Programa</th>
            <th class="px-3 py-2 text-left">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($inscricoesPorPrograma as $lin)
            <tr>
              <td class="px-3 py-2">{{ $lin->programa->nome ?? ('#'.$lin->programa_id) }}</td>
              <td class="px-3 py-2">{{ $lin->total }}</td>
            </tr>
          @empty
            <tr><td colspan="2" class="px-3 py-6 text-center text-gray-500">Sem dados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-6">
      <h3 class="font-semibold mb-2">Status globais</h3>
      <div class="grid md:grid-cols-4 gap-3">
        @forelse($statusGlobais as $st => $qt)
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="text-xs text-gray-500">{{ ucfirst($st) }}</div>
            <div class="text-2xl font-bold">{{ $qt }}</div>
          </div>
        @empty
          <div class="text-gray-500">Sem dados.</div>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endsection
