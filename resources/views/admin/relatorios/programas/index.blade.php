@extends('layouts.app')
@section('title','Relatórios — Programas Sociais')

@section('content')
<style>[x-cloak]{display:none!important}</style>

<div class="max-w-7xl mx-auto px-6 py-8" x-data="ChartPage('12m')" x-init="boot()">

  {{-- Nav entre páginas --}}
  <div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('admin.relatorios.programas.index') }}" class="px-3 py-2 rounded-xl text-white bg-green-700">Programas</a>
    <a href="{{ route('admin.relatorios.cidadaos.index') }}" class="px-3 py-2 rounded-xl bg-white border">Cidadãos</a>
    <a href="{{ route('admin.relatorios.assistentes.index') }}" class="px-3 py-2 rounded-xl bg-white border">Assistentes</a>
    <a href="{{ route('admin.relatorios.restaurante.index') }}" class="px-3 py-2 rounded-xl bg-white border">Restaurante</a>
  </div>

  {{-- Cabeçalho + período --}}
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-green-900">Programa social</h1>
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
      <h3 class="font-semibold text-gray-800 mb-2">Inscrições (período)</h3>
      <div class="h-72">
        <canvas id="progEvo" data-type="line"
                data-route="{{ route('admin.relatorios.programas.evolucao') }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4">
      <h3 class="font-semibold text-gray-800 mb-2">Funil do Programa</h3>
      <div class="h-72">
        <canvas id="progFunil" data-type="bar"
                data-route="{{ route('admin.relatorios.programas.funil') }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4">
      <h3 class="font-semibold text-gray-800 mb-2">Status das Inscrições</h3>
      <div class="h-72">
        <canvas id="progStatus" data-type="pie"
                data-route="{{ route('admin.relatorios.programas.status') }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4">
      <h3 class="font-semibold text-gray-800 mb-2">Inscrições por Região</h3>
      <div class="h-72">
        <canvas id="progReg" data-type="bar"
                data-route="{{ route('admin.relatorios.programas.regioes') }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold text-gray-800">Top Programas</h3>
        <span class="text-xs text-gray-500">Clique nas barras</span>
      </div>
      <div class="h-72">
        <canvas id="progTop" data-type="bar"
                data-route="{{ route('admin.relatorios.programas.top') }}"
                data-dash-template="{{ route('admin.relatorios.programas.dashboard', ['programa'=>'__PID__']) }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4">
      <h3 class="font-semibold text-gray-800 mb-2">Demografia (Gênero)</h3>
      <div class="h-72">
        <canvas id="progDemo" data-type="doughnut"
                data-route="{{ route('admin.relatorios.programas.demografia') }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4 md:col-span-2">
      <h3 class="font-semibold text-gray-800 mb-2">Origem da Inscrição</h3>
      <div class="h-72">
        <canvas id="progOrigem" data-type="bar"
                data-route="{{ route('admin.relatorios.programas.origem') }}"></canvas>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4 md:col-span-2">
      <h3 class="font-semibold text-gray-800 mb-2">SLA de Atendimento (dias)</h3>
      <div class="h-72">
        <canvas id="progSla" data-type="bar"
                data-route="{{ route('admin.relatorios.programas.sla') }}"></canvas>
      </div>
    </div>
  </div>
</div>

{{-- libs + loader --}}
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function ChartPage(initial='12m'){
  return {
    periodo: initial, charts: {},
    boot(){ this.loadAll(); },
    reload(){ this.loadAll(true); },
    buildUrl(u){ const url=new URL(u,location.origin); url.searchParams.set('periodo',this.periodo); return url; },
    async loadAll(force=false){
      for(const cv of document.querySelectorAll('canvas[data-route]')){
        const id=cv.id; if(this.charts[id] && !force) continue;
        const type=cv.dataset.type||'bar'; const url=this.buildUrl(cv.dataset.route);
        const res=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'}}); const json=await res.json();
        const ctx=cv.getContext('2d'); if(this.charts[id]) this.charts[id].destroy();
        this.charts[id]=new Chart(ctx,{type, data:{labels:json.labels||[], datasets:(json.datasets||[]).map(d=>({...d,borderWidth:2,tension:type==='line'?0.35:0}))}, options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'top'}}}});
        if(id==='progTop' && Array.isArray(json.ids)){ const dash=cv.dataset.dashTemplate; this.charts[id].options.onClick=(e,els)=>{ if(!els.length) return; const pid=json.ids[els[0].index]; if(pid){ location.href = dash.replace('__PID__', pid) + '?periodo='+encodeURIComponent(this.periodo); } }; this.charts[id].update(); }
      }
    }
  }
}
</script>
@endsection
