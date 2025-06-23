<?php

namespace Migrations\MigrationsGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Migrations\MigrationsGenerator\Services\DriverSelector;

class GenerateSeedsCommand extends Command
{
    protected $signature = 'generate:seeds';

    protected $description = 'Gera arquivos de seeds automaticamente a partir do banco de dados atual (MySQL ou PostgreSQL)';

    /**
     * Execute the console command.
     *
     * @param DriverSelector $selector
     * @return void
     */
    public function handle(DriverSelector $selector)
    {
        $this->info("Iniciando geração de arquivos de seed...");

        $driver = Config::get('database.default');
        $generator = $selector->select($driver);
        $generator->seeds();

        $this->info("Processo executado para o driver '{$driver}'.");
        $this->info('Geração de seeds concluída!');
    }
}