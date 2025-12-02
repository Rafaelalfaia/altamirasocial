@extends('layouts.app')
@section('title','Relatórios — Assistentes')

@section('content')
<style>[x-cloak]{display:none!important}</style>

<div class="max-w-7xl mx-auto px-6 py-8" x-data="ChartPage('12m')" x-init="boot()">

  <div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('admin.relatorios.programas.index') }}" class="px-3 py-2 rounded-xl bg-white border">Programas</a>
    <a href="{{ route('admin.relatorios.cidadaos.index') }}" class="px-3 py-2 rounded-xl bg-white border">Cidadãos</a>
    <a href="{{ route('admin.relatorios.assistentes.index') }}" class="px-3 py-2 rounded-xl text-white bg-green-700">Assistentes</a>
    <a href="{{ route('admin.relatorios.restaurante.index') }}" class="px-3 py-2 rounded-xl bg-white border">Restaurante</a>
  </div>

  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-green-900">Assistente</h1>
    <div class="flex items-center gap-3">
      <label class="text-sm text-gray-600">Período</label>
      <select class="border rounded-lg px-3 py-2 text-sm" x-model="periodo" @change="reload()">
        <option value="7d">7 dias</option><option value="30d">30 dias</option>
        <option value="90d">90 dias</option><option value="12m">12 meses</option>
        <option value="24m">24 meses</option><option value="all">Todos</option>
      </select>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-2xl shadow p-4">
      <h3 class="font-semibold text-gray-800 mb-2">Assistentes ativos (cadastros/mês)</h3>
      <div class="h-72">
        <canvas id="assAtivos" data-type="line"
                data-route="{{ route('admin.relatorios.assistentes.ativos') }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4">
      <h3 class="font-semibold text-gray-800 mb-2">Ranking por atividade</h3>
      <div class="h-72">
        <canvas id="assRanking" data-type="bar"
                data-route="{{ route('admin.relatorios.assistentes.ranking') }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4">
      <h3 class="font-semibold text-gray-800 mb-2">Plantão</h3>
      <div class="h-72">
        <canvas id="assPlantao" data-type="doughnut"
                data-route="{{ route('admin.relatorios.assistentes.plantao') }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4 md:col-span-2">
      <h3 class="font-semibold text-gray-800 mb-2">Tempo médio para responder (h)</h3>
      <div class="h-72">
        <canvas id="assResp" data-type="bar"
                data-route="{{ route('admin.relatorios.assistentes.respostas') }}"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function ChartPage(initial='12m'){
  return {
    periodo: initial, charts: {},
    boot(){ this.loadAll(); }, reload(){ this.loadAll(true); },
    buildUrl(u){ const url=new URL(u,location.origin); url.searchParams.set('periodo',this.periodo); return url; },
    async loadAll(force=false){
      for(const cv of document.querySelectorAll('canvas[data-route]')){
        const id=cv.id; if(this.charts[id] && !force) continue;
        const type=cv.dataset.type||'bar'; const url=this.buildUrl(cv.dataset.route);
        const res=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'}}); const json=await res.json();
        const ctx=cv.getContext('2d'); if(this.charts[id]) this.charts[id].destroy();
        this.charts[id]=new Chart(ctx,{type, data:{labels:json.labels||[], datasets:(json.datasets||[]).map(d=>({...d,borderWidth:2,tension:type==='line'?0.35:0}))}, options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'top'}}}});
      }
    }
  }
}
</script>
@endsection
