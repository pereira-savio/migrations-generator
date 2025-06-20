<?php

namespace Migrations\MigrationsGenerator\Contracts;

interface MigrationGeneratorInterface
{
    /**
     * Gera as migrations para o banco de dados.
     *
     * @return void
     */
    public function generate(): void;

    /**
     * Mapeia o tipo de coluna do banco de dados para o tipo correspondente no Laravel.
     *
     * @param string $dbType Tipo da coluna no banco de dados
     * @param string $driver Nome do driver do banco de dados (mysql, mariadb, pgsql)
     * @return string Tipo de coluna correspondente no Laravel
     */
    public function mapColumnType(string $dbType, string $driver): string;
}