<?php

namespace Migrations\MigrationsGenerator\Contracts;

interface MigrationGeneratorInterface
{
    /**
     * Gera as migrations para o banco de dados.
     *
     * @return void
     */
    public function migrations(): void;

    /**
     * Gera as migrations para o banco de dados.
     *
     * @return void
     */
    public function seeds(): void;

    /**
     * Encontra todas as tabelas do banco de dados atual, ignorando aquelas que devem ser puladas.
     *
     * @return array Lista de nomes das tabelas encontradas
     */
    public function findTables(): array;

    /**
     * Gera as migrations para todas as tabelas do banco de dados.
     *
     * @return void
     */
    public function generateMigrations(): void;
    
    /**
     * Gera os arquivos de migration para cada tabela encontrada no banco de dados.
     *
     * @return void
     */
    public function generateSeeds(): void;

    /**
     * Mapeia o tipo de coluna do banco de dados para o tipo correspondente no Laravel.
     *
     * @param string $dbType Tipo da coluna no banco de dados
     * @param string $driver Nome do driver do banco de dados (mysql, mariadb, pgsql)
     * @return string Tipo de coluna correspondente no Laravel
     */
    public function mapColumnType(string $dbType, string $driver): string;
}