<?php

namespace Migrations\MigrationsGenerator\Services;

use Migrations\MigrationsGenerator\Message;

class BasePath
{
    /**
     * Retorna o caminho completo do arquivo de migration.
     *
     * @param  string  $fileName  Nome do arquivo de migration
     * @return string Caminho completo do arquivo
     */
    public function migrationsPath(string $fileName): string
    {
        try {
            if (! is_dir(base_path('database/migrations'))) {
                mkdir(base_path('database/migrations'), 0755, true);
            }
        } catch (\Exception $e) {
            echo Message::error('Erro ao criar o diretório de migrations: '.$e->getMessage());
            exit;
        }

        return base_path('database/migrations/'.$fileName);
    }

    /**
     * Retorna o caminho completo do arquivo de seed.
     *
     * @param  string  $fileName  Nome do arquivo de seed
     * @return string Caminho completo do arquivo
     */
    public function seedersPath(string $fileName): string
    {
        try {
            if (! is_dir(base_path('database/seeders'))) {
                mkdir(base_path('database/seeders'), 0755, true);
            }
        } catch (\Exception $e) {
            echo Message::error('Erro ao criar o diretório de seeders: '.$e->getMessage());
            exit;
        }

        return base_path('database/seeders/'.$fileName);
    }
}
