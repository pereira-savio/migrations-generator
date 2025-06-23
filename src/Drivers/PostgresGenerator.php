<?php

namespace Migrations\MigrationsGenerator\Drivers;

use Illuminate\Support\Facades\DB;
use Migrations\MigrationsGenerator\Contracts\MigrationGeneratorInterface;
use Migrations\MigrationsGenerator\Files\Migrations;
use Migrations\MigrationsGenerator\Files\Seeds;
use Migrations\MigrationsGenerator\Message;
use Migrations\MigrationsGenerator\Services\SkipMigrationsTableFilter;

class PostgresGenerator implements MigrationGeneratorInterface
{
    protected SkipMigrationsTableFilter $filter;

    protected Migrations $migrations;

    protected Seeds $seeds;

    public function __construct()
    {
        $this->filter = new SkipMigrationsTableFilter;
        $this->migrations = new Migrations;
        $this->seeds = new Seeds;
    }

    /**
     * Gera as migrations para todas as tabelas do banco de dados PostgreSQL.
     */
    public function migrations(): void
    {
        $this->generateMigrations();
    }

    /**
     * Gera as seeds para todas as tabelas do banco de dados PostgreSQL.
     */
    public function seeds(): void
    {
        $this->generateSeeds();
    }

    /**
     * Obtém o nome do banco de dados atual.
     *
     * @return string Nome do banco de dados
     */
    public function getDatabaseName(): string
    {
        return DB::getDatabaseName();
    }

    /**
     * Encontra todas as tabelas do banco de dados atual, ignorando aquelas que devem ser puladas.
     *
     * @return array Lista de nomes das tabelas encontradas
     */
    public function findTables(): array
    {
        $schemaName = $this->getDatabaseName();
        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = $schemaName AND table_type='BASE TABLE'");
        $tableNames = [];

        foreach ($tables as $table) {
            $tableName = $table->table_name;

            // Pular tabelas que devem ser ignoradas
            if ($this->filter->shouldSkip($tableName)) {
                continue;
            }

            $tableNames[] = $tableName;
        }

        return $tableNames;
    }

    /**
     * Gera as migrations para cada tabela encontrada no banco de dados.
     */
    public function generateMigrations(): void
    {
        $schemaName = $this->getDatabaseName();
        $tables = $this->findTables();

        foreach ($tables as $table) {

            echo Message::info($table, 'Gerando migration para a tabela:');

            // Recupera as colunas da tabela no PostgreSQL
            $columns = DB::select(
                "SELECT column_name, data_type, is_nullable, column_default
                    FROM information_schema.columns
                    WHERE table_schema = $schemaName AND table_name = ?",
                [$table]
            );

            $schemaFields = '';

            foreach ($columns as $column) {
                // Propriedades da coluna: column_name, data_type, is_nullable, column_default
                $field = $column->column_name;
                $type = $this->mapColumnType($column->data_type, 'pgsql');
                $nullable = ($column->is_nullable === 'YES') ? '->nullable()' : '';
                $default = ($column->column_default !== null) ? "->default('".addslashes($column->column_default)."')" : '';

                // Verifica se a coluna é serial (auto incremento) verificando 'nextval' na coluna default
                if ($column->column_default !== null && strpos($column->column_default, 'nextval') !== false) {
                    $schemaFields .= "\$table->increments('$field');\n\t\t\t";
                } else {
                    $schemaFields .= "\$table->$type('$field')$nullable$default;\n\t\t\t";
                }
            }

            $this->migrations->generate($table, $schemaFields);
            sleep(1);
        }
    }

    /**
     * Gera as seeds para cada tabela encontrada no banco de dados.
     */
    public function generateSeeds(): void
    {
        $tables = $this->findTables();

        foreach ($tables as $tableName) {

            echo Message::info($tableName, 'Gerando seed para a tabela:');

            // Recupera os dados da tabela no PostgreSQL
            $rows = DB::select("SELECT * FROM \"$tableName\"");
            if (empty($rows)) {
                echo Message::warning($tableName, '-- Tabela vazia, seed não gerada.');

                continue;
            }

            $seedData = [];

            foreach ($rows as $row) {
                $seedData[] = (array) $row;
            }

            echo Message::info($tableName, '-- Gerando seed com '.count($seedData).' registros.');

            $this->seeds->generate($tableName, $seedData);
            sleep(1);
        }
    }

    /**
     * Mapeia o tipo de coluna do PostgreSQL para o tipo correspondente no Laravel.
     *
     * @param  string  $dbType  Tipo da coluna no banco de dados
     * @param  string  $driver  Nome do driver (neste caso, 'pgsql')
     * @return string Tipo correspondente no Laravel
     */
    public function mapColumnType(string $dbType, string $driver): string
    {
        if (in_array($dbType, ['integer', 'bigint', 'smallint'])) {
            return 'integer';
        } elseif (strpos($dbType, 'character varying') !== false || $dbType === 'varchar') {
            return 'string';
        } elseif (strpos($dbType, 'text') !== false) {
            return 'text';
        } elseif (strpos($dbType, 'longText') !== false) {
            return 'longText';
        } elseif (strpos($dbType, 'timestamp') !== false) {
            return 'dateTime';
        } elseif ($dbType === 'date') {
            return 'date';
        } elseif (in_array($dbType, ['numeric', 'decimal'])) {
            return 'decimal';
        } else {
            return 'string';
        }
    }
}
