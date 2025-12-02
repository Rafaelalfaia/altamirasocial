@extends('layouts.app')
@section('title','Informações: '.$programa->nome)

@section('content')
<div class="max-w-6xl mx-auto p-4 md:p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold">Informações do Programa</h1>
    <a href="{{ route('admin.programas.show',$programa) }}" class="text-sm text-gray-600 hover:underline">Voltar</a>
  </div>

  <div class="bg-white rounded-xl shadow border p-4">
    <div class="grid md:grid-cols-3 gap-4">
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="text-xs text-gray-500">Inscrições (total)</div>
        <div class="text-3xl font-bold">{{ $inscritosTotal }}</div>
      </div>

      @foreach($porStatus as $st => $qt)
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="text-xs text-gray-500">Status: {{ ucfirst($st) }}</div>
          <div class="text-3xl font-bold">{{ $qt }}</div>
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      <h3 class="font-semibold mb-2">Inscrições por dia (últimos 60)</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr><th class="px-3 py-2 text-left">Dia</th><th class="px-3 py-2 text-left">Total</th></tr>
          </thead>
          <tbody class="divide-y">
            @forelse($porDia as $l)
              <tr><td class="px-3 py-2">{{ \Illuminate\Support\Carbon::parse($l->dia)->format('d/m/Y') }}</td><td class="px-3 py-2">{{ $l->total }}</td></tr>
            @empty
              <tr><td colspan="2" class="px-3 py-6 text-center text-gray-500">Sem dados.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
