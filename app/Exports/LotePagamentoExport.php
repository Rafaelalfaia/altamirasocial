<?php

namespace App\Exports;

use App\Models\LotePagamento;
use App\Models\ProgramaInscricao;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class LotePagamentoExport implements
    FromCollection,
    ShouldAutoSize,
    WithStyles,
    WithDrawings,
    WithEvents,
    WithTitle,
    WithCustomStartCell
{
    use Exportable;

    private $lote;
    private $dados;

    public function __construct(LotePagamento $lote)
    {
        $this->lote = $lote;
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function collection()
{
    if ($this->dados) return $this->dados;

    $this->ordem = 0;

    // Inscrições aprovadas do programa associado ao lote
    $inscricoes = ProgramaInscricao::with('cidadao')
        ->where('programa_id', $this->lote->programa_id)
        ->where('status', 'APROVADO')
        ->get();

    // Regiões permitidas pelo programa (array do JSON)
    $regioesPermitidas = $this->lote->programa->regioes ?? [];
    $regioesPermitidas = array_map(fn($r) => strtolower(trim($r)), $regioesPermitidas);

    // Filtra pela origem da inscrição
    $filtradas = $inscricoes->filter(function ($inscricao) use ($regioesPermitidas) {
        $regiaoInscricao = strtolower(trim($inscricao->regiao));
        return in_array($regiaoInscricao, $regioesPermitidas);
    });

    // Ordena e mapeia os dados
    $this->dados = $filtradas->sortBy(fn($i) => $i->cidadao->nome)
        ->map(function ($inscricao) {
            $cidadao = $inscricao->cidadao;
            $cpfNumeros = preg_replace('/\D/', '', $cidadao->cpf);
            $cpf = $this->lote->formato_cpf === 'com_pontos'
                ? substr($cpfNumeros, 0, 3) . '.' . substr($cpfNumeros, 3, 3) . '.' . substr($cpfNumeros, 6, 3) . '-' . substr($cpfNumeros, 9, 2)
                : $cpfNumeros;

            return [
                ++$this->ordem, // ORD
                $cidadao->nome,
                $cpf,
                \Carbon\Carbon::parse($cidadao->data_nascimento)->format('d/m/Y'),
                $cidadao->telefone,
                $this->lote->periodo_pagamento,
                'R$ ' . number_format($this->lote->valor_pagamento, 2, ',', '.'),
                $this->lote->programa->nome,
            ];
        })->values();

    return $this->dados;
}


    public function drawings()
    {
        $logoEsq = new Drawing();
        $logoEsq->setName('Logo Esquerda');
        $logoEsq->setPath(public_path('imagens/logolote.png'));
        $logoEsq->setHeight(55);
        $logoEsq->setCoordinates('A1');
        $logoEsq->setOffsetX(15)->setOffsetY(9);

        $logoDir = new Drawing();
        $logoDir->setName('Logo Direita');
        $logoDir->setPath(public_path('imagens/logolote1.png'));
        $logoDir->setHeight(30);
        $logoDir->setCoordinates('H1');
        $logoDir->setOffsetX(1)->setOffsetY(20);

        return [$logoEsq, $logoDir];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'B' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'C' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'D' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'E' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'F' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'G' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'H' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $dados = $this->collection();
                $total = $dados->count();
                $linhaFinal = 6 + $total;

                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', $this->lote->programa->nome);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->mergeCells('A3:H3');
                $sheet->setCellValue('A3', 'Data do Lote: ' . Carbon::parse($this->lote->data_envio)->format('d/m/Y') . ' — Previsão de Pagamento: ' . Carbon::parse($this->lote->previsao_pagamento)->format('d/m/Y'));
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A4:H4');
                $sheet->setCellValue('A4', 'Período: ' . $this->lote->periodo_pagamento);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A5:H5');
                $sheet->setCellValue('A5', 'Região: ' . $this->lote->regiao . ' — Quantidade de Famílias Beneficiadas: ' . $total);
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Cabeçalho
                $sheet->setCellValue('A6', 'ORD');
                $sheet->setCellValue('B6', 'Nome Completo');
                $sheet->setCellValue('C6', 'CPF');
                $sheet->setCellValue('D6', 'Nascimento');
                $sheet->setCellValue('E6', 'Telefone');
                $sheet->setCellValue('F6', 'Período');
                $sheet->setCellValue('G6', 'Valor');
                $sheet->setCellValue('H6', 'Programa');

                $sheet->getStyle('A6:H6')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD9D9D9'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle("A7:H{$linhaFinal}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Total
                $valorTotal = $this->lote->valor_pagamento * $total;
                $linhaTotal = $linhaFinal + 1;
                $sheet->mergeCells("A{$linhaTotal}:F{$linhaTotal}");
                $sheet->setCellValue("A{$linhaTotal}", 'Total Geral:');
                $sheet->setCellValue("G{$linhaTotal}", 'R$ ' . number_format($valorTotal, 2, ',', '.'));
                $sheet->getStyle("A{$linhaTotal}:G{$linhaTotal}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // Rodapé
                $linhaRodape = $linhaTotal + 3;
                $sheet->mergeCells("A{$linhaRodape}:H{$linhaRodape}");
                $sheet->setCellValue("A{$linhaRodape}", 'EUNÉDIA DA SILVA ARAÚJO');
                $sheet->mergeCells("A" . ($linhaRodape + 1) . ":H" . ($linhaRodape + 1));
                $sheet->setCellValue("A" . ($linhaRodape + 1), 'Secretária Municipal de Assistência e Promoção Social');
                $sheet->getStyle("A{$linhaRodape}:A" . ($linhaRodape + 1))->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'font' => ['bold' => true],
                ]);
            }
        ];
    }

    public function title(): string
    {
        return mb_strimwidth($this->lote->nome, 0, 31, '');
    }
}
