@extends('layouts.app')
@section('title','Relatórios — Programa')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
  <h1 class="text-2xl font-bold text-green-900 mb-4">Programa: {{ $programa->nome }}</h1>

  @include('admin.relatorios.programas._filtros', ['fixo' => $programa->id])

  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6">
    <x-chart-card id="sEvo" titulo="Inscrições (período)" type="line" data-url="{{ route('admin.relatorios.programas.show.evolucao', $programa) }}{{ request()->getQueryString() ? ('?'.request()->getQueryString()) : '' }}" />
    <x-chart-card id="sFunil" titulo="Funil" type="bar" data-url="{{ route('admin.relatorios.programas.show.funil', $programa) }}{{ request()->getQueryString() ? ('?'.request()->getQueryString()) : '' }}" />
    <x-chart-card id="sStatus" titulo="Status" type="pie" data-url="{{ route('admin.relatorios.programas.show.status', $programa) }}{{ request()->getQueryString() ? ('?'.request()->getQueryString()) : '' }}" />
    <x-chart-card id="sReg" titulo="Por Região" type="bar" data-url="{{ route('admin.relatorios.programas.show.regioes', $programa) }}{{ request()->getQueryString() ? ('?'.request()->getQueryString()) : '' }}" />
    <x-chart-card id="sDemo" titulo="Demografia" type="doughnut" data-url="{{ route('admin.relatorios.programas.show.demografia', $programa) }}{{ request()->getQueryString() ? ('?'.request()->getQueryString()) : '' }}" />
    <x-chart-card id="sSla" titulo="SLA (dias)" type="bar" data-url="{{ route('admin.relatorios.programas.show.sla', $programa) }}{{ request()->getQueryString() ? ('?'.request()->getQueryString()) : '' }}" />
  </div>
</div>
@endsection
