<?php

namespace Migrations\MigrationsGenerator\Services;

/**
 * Filtra tabelas que não devem ser processadas pelo gerador de migrations.
 */
class SkipMigrationsTableFilter
{
    /**
     * Verifica se a tabela deve ser pulada.
     *
     * @return bool Retorna true se deve pular (skip)
     */
    public function shouldSkip(string $tableName): bool
    {
        return $this->tables($tableName) || $this->initialNames($tableName);
    }

    //TODO: Busca de dados na .env para definir quais tabelas devem ser ignoradas
    public function tables(string $tableName): bool
    {
        // Tabelas que devem ser ignoradas
        $skipTables =  [
            'migrations',
            'users',
        ];

        return in_array($tableName, $skipTables);
    }

    //TODO: Busca de dados na .env para definir quais nomes iniciais de tabelas devem ser ignorados
    public function initialNames(string $tableName): bool
    {
        // Nomes iniciais de tabelas que devem ser ignorados
        $skipNames = [
            'laravel_',
        ];

        foreach ($skipNames as $name) {
            if (str_starts_with($tableName, $name)) {
                return true;
            }
        }
        return false;
    }
}