<?php

namespace Migrations\MigrationsGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Migrations\MigrationsGenerator\Services\DriverSelector;

class GenerateMigrationsCommand extends Command
{
    protected $signature = 'generate:migrations';

    protected $description = 'Gera arquivos de migrations automaticamente a partir do banco de dados atual (MySQL ou PostgreSQL)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(DriverSelector $selector)
    {
        $this->info('Iniciando geração de arquivos de migrations...');

        $driver = Config::get('database.default');
        $generator = $selector->select($driver);
        $generator->migrations();

        $this->info("Processo executado para o driver '{$driver}'.");
        $this->info('Geração de migrations concluída!');
    }
}