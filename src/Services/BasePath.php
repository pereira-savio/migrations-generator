<?php

namespace Migrations\MigrationsGenerator\Services;

use Migrations\MigrationsGenerator\Files\Path;
use Migrations\MigrationsGenerator\Message;

class BasePath
{
    private Path $path;

    public function __construct()
    {
        $this->path = new Path;
    }

    /**
     * Retorna o caminho completo do arquivo de migration.
     *
     * @param  string  $fileName  Nome do arquivo de migration
     * @return string Caminho completo do arquivo
     */
    public function migrationsPath(string $fileName): string
    {
        try {

            $basePath = $this->path->databasePath('migrations/New/');

            $this->path->validatePath($basePath);

        } catch (\Exception $e) {
            echo Message::error('Erro ao criar o diretório de migrations: '.$e->getMessage());
            exit;
        }

        return $basePath.$fileName;
    }

    /**
     * Retorna o caminho completo do arquivo de seeders.
     *
     * @param  string  $fileName  Nome do arquivo de seeders
     * @return string Caminho completo do arquivo
     */
    public function seedersPath(string $fileName): string
    {
        try {

            $basePath = $this->path->databasePath('seeders/New/');
            $this->path->validatePath($basePath);

        } catch (\Exception $e) {
            echo Message::error('Erro ao criar o diretório de seeders: '.$e->getMessage());
            exit;
        }

        return $basePath.$fileName;
    }
}