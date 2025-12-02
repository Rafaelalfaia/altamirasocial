@extends('layouts.app')
@section('title', 'Relatórios - Cidadãos')

@section('content')
<style>
  .chart-card{ min-height:320px; }
  .chart-card--tall{ min-height:420px; }
</style>

<div class="max-w-7xl mx-auto px-4 py-8 space-y-8">

  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold text-gray-800">👥 Relatório de Cidadãos</h1>
    <a href="{{ route('admin.relatorios.index') }}" class="text-sm text-green-700 hover:underline">← Voltar</a>
  </div>

  {{-- Filtros (GET simples, página recarrega já com os dados prontos) --}}
  <form method="GET" class="bg-white rounded-xl p-4 ring-1 ring-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
      <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-600">Buscar (nome/CPF)</label>
        <input type="text" name="busca" value="{{ $f['busca'] }}" class="w-full border rounded px-3 py-2" placeholder="Ex.: Maria, 123.456...">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600">Período: de</label>
        <input type="date" name="de" value="{{ $f['de'] }}" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600">até</label>
        <input type="date" name="ate" value="{{ $f['ate'] }}" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600">Status</label>
        <select name="status" class="w-full border rounded px-3 py-2">
          <option value="">Todos</option>
          @foreach($opts['status'] as $opt)
            <option value="{{ $opt }}" @selected($f['status']===$opt)>{{ ucfirst($opt) }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600">Sexo</label>
        <select name="sexo" class="w-full border rounded px-3 py-2">
          <option value="">Todos</option>
          @foreach($opts['sexo'] as $opt)
            <option value="{{ $opt }}" @selected($f['sexo']===$opt)>{{ $opt }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600">Bairro</label>
        <select name="bairro_id" class="w-full border rounded px-3 py-2">
          <option value="">Todos</option>
          @foreach($bairros as $b)
            <option value="{{ $b->id }}" @selected($f['bairro_id']==$b->id)>{{ $b->nome }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600">PCD</label>
        <select name="pcd" class="w-full border rounded px-3 py-2">
          <option value="">Todos</option>
          <option value="1" @selected($f['pcd']==='1')>Sim</option>
          <option value="0" @selected($f['pcd']==='0')>Não</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600">Escolaridade</label>
        <select name="escolaridade" class="w-full border rounded px-3 py-2">
          <option value="">Todas</option>
          @foreach($opts['escolaridade'] as $opt)
            <option value="{{ $opt }}" @selected($f['escolaridade']===$opt)>{{ $opt }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-600">Situação Profissional</label>
        <select name="situacao_profissional" class="w-full border rounded px-3 py-2">
          <option value="">Todas</option>
          @foreach($opts['situacao_profissional'] as $opt)
            <option value="{{ $opt }}" @selected($f['situacao_profissional']===$opt)>{{ $opt }}</option>
          @endforeach
        </select>
      </div>
      <div class="md:col-span-2 flex items-end gap-2">
        <button class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow text-sm">Atualizar gráficos</button>
        <a href="{{ route('admin.relatorios.cidadaos.index') }}" class="px-4 py-2 rounded border text-sm">Limpar</a>
      </div>
    </div>
  </form>

  {{-- Totais --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl p-4 ring-1 ring-gray-200"><div class="text-xs text-gray-500">Total</div><div class="text-2xl font-bold text-gray-800">{{ number_format($totais['total'],0,',','.') }}</div></div>
    <div class="bg-white rounded-xl p-4 ring-1 ring-gray-200"><div class="text-xs text-gray-500">PCD</div><div class="text-2xl font-bold text-gray-800">{{ number_format($totais['total_pcd'],0,',','.') }}</div></div>
    <div class="bg-white rounded-xl p-4 ring-1 ring-gray-200"><div class="text-xs text-gray-500">Período</div><div class="text-sm text-gray-700">{{ $f['de'] ?: '—' }} → {{ $f['ate'] ?: '—' }}</div></div>
  </div>

  {{-- Grade de gráficos --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_status"></div>
    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_sexo"></div>

    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_faixa"></div>
    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_mes"></div>

    <div class="chart-card chart-card--tall bg-white rounded-xl p-4 ring-1 ring-gray-200 lg:col-span-2" id="ch_bairros"></div>

    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_escolaridade"></div>
    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_pcd"></div>

    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_renda"></div>
    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_pessoas"></div>

    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_sitprof"></div>
    <div class="chart-card bg-white rounded-xl p-4 ring-1 ring-gray-200" id="ch_estadocivil"></div>
  </div>
</div>

{{-- ApexCharts CDN --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  const CH = @json($charts);

  const donut = (id, title, payload) => {
    if(!payload) return;
    new ApexCharts(document.querySelector('#'+id), {
      chart:{ type:'donut', height: document.querySelector('#'+id).clientHeight || 320 },
      title:{ text:title },
      labels: payload.labels, series: payload.data,
      legend:{ position:'bottom' },
      colors:['#065f46','#047857','#059669','#16a34a','#22c55e','#4ade80','#34d399','#6ee7b7','#a7f3d0','#d1fae5']
    }).render();
  };

  const bar = (id, title, payload, horizontal=false) => {
    if(!payload) return;
    new ApexCharts(document.querySelector('#'+id), {
      chart:{ type:'bar', height: document.querySelector('#'+id).clientHeight || 320, toolbar:{show:false} },
      title:{ text:title },
      series:[{ name:'Qtd.', data: payload.data }],
      xaxis:{ categories: payload.labels },
      plotOptions:{ bar:{ horizontal } },
      dataLabels:{ enabled:false },
      colors:['#10b981']
    }).render();
  };

  const line = (id, title, payload) => {
    if(!payload) return;
    new ApexCharts(document.querySelector('#'+id), {
      chart:{ type:'line', height: document.querySelector('#'+id).clientHeight || 320, toolbar:{show:false} },
      title:{ text:title },
      series:[{ name:'Cadastros', data: payload.data }],
      xaxis:{ categories: payload.labels },
      stroke:{ curve:'smooth', width:3 },
      markers:{ size:3 },
      colors:['#065f46']
    }).render();
  };

  // Render
  donut('ch_status','Status dos Cadastros', CH.status);
  donut('ch_sexo','Distribuição por Sexo', CH.sexo);

  bar('ch_faixa','Faixa Etária', CH.faixa_etaria, false);
  line('ch_mes','Cadastros por Mês', CH.por_mes);

  bar('ch_bairros','Top 10 Bairros', CH.top_bairros, true);

  bar('ch_escolaridade','Escolaridade', CH.escolaridade, false);
  donut('ch_pcd','PCD', CH.pcd);

  bar('ch_renda','Faixas de Renda', CH.renda, false);
  bar('ch_pessoas','Pessoas na Residência', CH.pessoas_residencia, false);

  bar('ch_sitprof','Situação Profissional', CH.situacao_profissional, false);
  bar('ch_estadocivil','Estado Civil', CH.estado_civil, false);
</script>
@endsection
