@php
    $agora = now()->format('d/m/Y H:i');
    $escopo = $metrics['escopo'] ?? 'filtro';
@endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Relatório de Inscrições — {{ $programa->nome }}</title>
<style>
    * { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    body { font-size: 12px; color: #111; }
    h1 { font-size: 18px; margin: 0 0 6px 0; }
    h2 { font-size: 14px; margin: 14px 0 6px 0; }
    .muted { color:#666; }
    .pill { display:inline-block; padding:2px 6px; border-radius: 10px; font-size:10px; font-weight:bold; }
    .pill.aprovado { background:#d1fae5; color:#065f46; }
    .pill.pendente { background:#fef3c7; color:#92400e; }
    .pill.reprovado { background:#fee2e2; color:#991b1b; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #ddd; padding:6px 8px; }
    th { background:#f3f4f6; text-align:left; }
    .right { text-align:right; }
    .center { text-align:center; }
    .mb8 { margin-bottom:8px; }
    .mb12 { margin-bottom:12px; }
</style>
</head>
<body>

{{-- Cabeçalho --}}
<div class="mb8">
    <h1>Relatório de Inscrições — {{ $programa->nome }}</h1>
    <div class="muted">
        Gerado em {{ $agora }}
        @if($escopo === 'selecionados')
            · Escopo: Seleção manual ({{ $metrics['total'] }} registro(s))
        @else
            · Escopo: Filtro atual
        @endif
    </div>
    @if(!empty($filtros))
        <div class="muted">
            Filtros:
            @if($filtros['status']) Status={{ ucfirst($filtros['status']) }}; @endif
            @if($filtros['regiao']) Região={{ $filtros['regiao'] }}; @endif
            @if($filtros['q']) Busca="{{ $filtros['q'] }}"; @endif
            Ordem={{ strtoupper($filtros['ordem'] ?? 'az') }}
        </div>
    @endif
</div>

{{-- Métricas --}}
<table class="mb12">
    <thead>
        <tr>
            <th>Total de Inscritos</th>
            <th class="center">Aprovados</th>
            <th class="center">Pendentes</th>
            <th class="center">Reprovados</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="right"><strong>{{ $metrics['total'] }}</strong></td>
            <td class="center">{{ $metrics['aprovado'] }}</td>
            <td class="center">{{ $metrics['pendente'] }}</td>
            <td class="center">{{ $metrics['reprovado'] }}</td>
        </tr>
    </tbody>
</table>

{{-- Lista de participantes --}}
<h2>Participantes</h2>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Inscrito</th>
            @if($programa->aceita_menores)
                <th>Responsável</th>
                <th>Parentesco</th>
            @else
                <th>CPF</th>
                <th>Telefone</th>
            @endif
            <th>Status</th>
            <th>Região</th>
            <th>Registrado em</th>
        </tr>
    </thead>
    <tbody>
        @forelse($inscricoes as $i => $inscricao)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>
                    {{ optional($inscricao->dependente)->nome ?? optional($inscricao->cidadao)->nome ?? '—' }}
                </td>

                @if($programa->aceita_menores)
                    <td>{{ optional($inscricao->cidadao)->nome ?? '—' }}</td>
                    <td>{{ ucfirst(optional($inscricao->dependente)->grau_parentesco ?? '—') }}</td>
                @else
                    <td>{{ optional($inscricao->cidadao)->cpf ?? '—' }}</td>
                    <td>{{ optional($inscricao->cidadao)->telefone ?? '—' }}</td>
                @endif

                <td>
                    <span class="pill {{ $inscricao->status }}">{{ ucfirst($inscricao->status) }}</span>
                </td>
                <td>{{ $inscricao->regiao ?? '—' }}</td>
                <td>{{ optional($inscricao->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="center muted">Nenhum registro.</td></tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
