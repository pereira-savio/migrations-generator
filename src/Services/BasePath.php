<?php

namespace Migrations\MigrationsGenerator\Services;

use Migrations\MigrationsGenerator\Message;

class BasePath
{
    /**
     * Retorna o caminho completo do arquivo de migration.
     *
     * @param string $fileName Nome do arquivo de migration
     * @return string Caminho completo do arquivo
     */
    public function file(string $fileName): string
    {
        try {
            if (!is_dir(base_path('database/migrations'))) {
                mkdir(base_path('database/migrations'), 0755, true);
            }
        } catch (\Exception $e) {
            echo Message::error("Erro ao criar o diretório de migrations: " . $e->getMessage());
            die;
        }

        return base_path('database/migrations/' . $fileName);
    }
}