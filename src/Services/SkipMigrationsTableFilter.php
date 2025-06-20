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
        return $tableName === 'migrations';
    }
}
