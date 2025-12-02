@php
  $periodo = request('periodo','12m');
  $status  = request('status','');
  $regiao  = request('regiao','');
  $pidFix  = $fixo ?? null;
@endphp
<form method="GET" class="bg-white rounded-xl shadow p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
  <div>
    <label class="block text-sm text-gray-600 mb-1">Período</label>
    <select name="periodo" class="w-full border rounded px-3 py-2">
      @foreach (['7d'=>'7 dias','30d'=>'30 dias','90d'=>'90 dias','12m'=>'12 meses','24m'=>'24 meses','all'=>'Todos'] as $k=>$v)
        <option value="{{ $k }}" @selected($periodo===$k)>{{ $v }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="block text-sm text-gray-600 mb-1">Status (múltiplos, vírgula)</label>
    <input name="status" value="{{ $status }}" placeholder="aprovado,finalizado" class="w-full border rounded px-3 py-2">
  </div>
  <div>
    <label class="block text-sm text-gray-600 mb-1">Região (múltiplas, vírgula)</label>
    <input name="regiao" value="{{ $regiao }}" placeholder="Centro,Sudeste" class="w-full border rounded px-3 py-2">
  </div>
  <div class="flex items-end">
    <button class="w-full bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Aplicar</button>
  </div>
  @if (!$pidFix)
    <div class="md:col-span-4 text-xs text-gray-500">Dica: para focar em 1 programa específico, entre pelo link do programa (tela “show”).</div>
  @endif
</form>
