<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendasAtendenteExport implements FromCollection, WithHeadings
{
    protected $vendas;

    public function __construct(Collection $vendas)
    {
        $this->vendas = $vendas;
    }

    public function collection()
    {
        return $this->vendas->map(function ($venda) {
            return [
                'Data' => $venda->data_venda->format('d/m/Y H:i'),
                'Cliente' => $venda->cidadao->nome ?? $venda->cidadaoTemporario->nome,
                'CPF' => $venda->cidadao->cpf ?? $venda->cidadaoTemporario->cpf,
                'Tipo' => ucfirst($venda->tipo_cliente),
                'Pratos' => $venda->numero_pratos,
                'Estudante' => $venda->estudante ? 'Sim' : 'Não',
                'Doação' => $venda->doacao ? 'Sim' : 'Não',
                'Consumo' => ucfirst($venda->tipo_consumo),
                'Pagamento' => ucfirst($venda->forma_pagamento),
            ];
        });
    }

    public function headings(): array
    {
        return ['Data', 'Cliente', 'CPF', 'Tipo', 'Pratos', 'Estudante', 'Doação', 'Consumo', 'Pagamento'];
    }
}

