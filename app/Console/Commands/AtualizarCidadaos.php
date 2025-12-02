<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Cidadao;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class AtualizarCidadaos extends Command
{
    protected $signature = 'atualizar:cidadaos';
    protected $description = 'Atualiza cadastros de cidadãos com base na planilha socioeconômica';

    public function handle()
    {
        $path = storage_path('app/imports/cidadaos.xlsx');

        if (!file_exists($path)) {
            $this->error("Arquivo não encontrado: {$path}");
            return;
        }

        $planilha = Excel::toCollection(null, $path)->first();

        $total = $planilha->count();
        $atualizados = 0;
        $naoEncontrados = 0;

        foreach ($planilha as $linha) {
            $cpf = preg_replace('/\D/', '', $linha['cpf'] ?? '');

            if (strlen($cpf) !== 11) {
                $this->warn("⚠️ CPF inválido: {$cpf}");
                $naoEncontrados++;
                continue;
            }

            $user = User::where('cpf', $cpf)->first();
            if (!$user) {
                $this->warn("Usuário não encontrado: {$cpf}");
                $naoEncontrados++;
                continue;
            }

            $cidadao = Cidadao::where('user_id', $user->id)->first();
            if (!$cidadao) {
                $this->warn("Cidadao não encontrado para CPF: {$cpf}");
                $naoEncontrados++;
                continue;
            }

            // Atualiza campos disponíveis
            $cidadao->update([
                'data_nascimento' => $linha['data_nascimento'] ?? null,
                'sexo' => $linha['sexo'] ?? null,
                'ocupacao' => $linha['qual a sua situação profissional atual?'] ?? null,
                'renda_total_familiar' => isset($linha['qual sua renda bruta familiar?']) ? floatval($linha['qual sua renda bruta familiar?']) : null,
                'pessoas_na_residencia' => isset($linha['quantas pessoas  residem  na casa incluindo crianças?']) ? intval($linha['quantas pessoas  residem  na casa incluindo crianças?']) : null,
                'pcd' => (strtoupper($linha['possui pessoas com deficiência'] ?? '') === 'SIM'),
                'tipo_deficiencia' => $linha['qual tipo de deficiência:\n(caso tenha marcado sim na pergunta anterior.)'] ?? null,
                'observacoes' => $linha['obervação'] ?? null,
            ]);

            $this->info("✅ Atualizado: {$cpf}");
            $atualizados++;
        }

        $this->newLine();
        $this->info("Processo concluído:");
        $this->line("Total na planilha: {$total}");
        $this->line("Atualizados: {$atualizados}");
        $this->line("Não encontrados: {$naoEncontrados}");
    }
}
