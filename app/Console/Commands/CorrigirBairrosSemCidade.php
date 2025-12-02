<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bairro;
use App\Models\Cidade;

class CorrigirBairrosSemCidade extends Command
{
    protected $signature = 'corrigir:bairros';
    protected $description = 'Corrige bairros sem cidade associada';

    public function handle()
    {
        $bairros = Bairro::whereNull('cidade_id')->orWhereDoesntHave('cidade')->get();

        if ($bairros->isEmpty()) {
            $this->info('✅ Todos os bairros estão com cidade definida corretamente.');
            return;
        }

        $this->warn("🔍 Encontramos {$bairros->count()} bairros sem cidade válida:");

        foreach ($bairros as $bairro) {
            $this->line("Bairro: {$bairro->nome} (ID: {$bairro->id})");

            $cidades = Cidade::orderBy('nome')->get();

            foreach ($cidades as $cidade) {
                $this->line("  [{$cidade->id}] {$cidade->nome} / {$cidade->estado->nome}");
            }

            $novaCidadeId = $this->ask("Digite o ID da cidade correta para o bairro '{$bairro->nome}'");

            $cidade = Cidade::find($novaCidadeId);
            if ($cidade) {
                $bairro->cidade_id = $cidade->id;
                $bairro->save();
                $this->info("✔️ Bairro '{$bairro->nome}' atualizado com cidade '{$cidade->nome}'");
            } else {
                $this->error("❌ Cidade com ID {$novaCidadeId} não encontrada. Pulando...");
            }
        }

        $this->info("🏁 Correção finalizada.");
    }
}
