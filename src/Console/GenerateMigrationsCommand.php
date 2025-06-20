<?php

namespace Migrations\MigrationsGenerator\Console;

use Illuminate\Console\Command;
use Migrations\MigrationsGenerator\Services\DriverSelector;
use Illuminate\Support\Facades\Config;

class GenerateMigrationsCommand extends Command
{
    protected $signature = 'generate:migrations';
    protected $description = 'Gera arquivos de migrations automaticamente a partir do banco de dados atual (MySQL ou PostgreSQL)';

    /**
     * Execute the console command.
     *
     * @param DriverSelector $selector
     * @return void
     */
    public function handle(DriverSelector $selector)
    {
        $this->info("Iniciando a geração de migrations...");

        $driver    = Config::get('database.default');
        $generator = $selector->select($driver);
        $generator->generate();
        
        $this->info("Migrations geradas para o driver '{$driver}'.");
        $this->info("Geração de migrations concluída!");
    }
}