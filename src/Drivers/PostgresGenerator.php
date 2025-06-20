<?php

namespace Migrations\MigrationsGenerator\Drivers;

use Illuminate\Support\Facades\DB;
use Migrations\MigrationsGenerator\Contracts\MigrationGeneratorInterface;
use Migrations\MigrationsGenerator\Message;
use Migrations\MigrationsGenerator\Services\GenerateMigrations;
use Migrations\MigrationsGenerator\Services\SkipMigrationsTableFilter;

class PostgresGenerator implements MigrationGeneratorInterface
{
    protected SkipMigrationsTableFilter $filter;

    protected GenerateMigrations $migration;

    public function __construct()
    {
        $this->filter = new SkipMigrationsTableFilter;
        $this->migration = new GenerateMigrations;
    }

    public function generate(): void
    {
        $schemaName = DB::getDatabaseName();

        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = $schemaName AND table_type='BASE TABLE'");

        foreach ($tables as $table) {
            $tableName = $table->table_name;

            // Pular tabelas que devem ser ignoradas
            if ($this->filter->shouldSkip($tableName)) {
                continue;
            }

            echo Message::info("Gerando migration para a tabela: $tableName");

            // Recupera as colunas da tabela no PostgreSQL
            $columns = DB::select(
                "SELECT column_name, data_type, is_nullable, column_default
                    FROM information_schema.columns
                    WHERE table_schema = $schemaName AND table_name = ?",
                [$tableName]
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

            $this->migration->generate($tableName, $schemaFields);
            sleep(1);
        }
    }

    public function mapColumnType(string $dbType, string $driver): string
    {
        if (in_array($dbType, ['integer', 'bigint', 'smallint'])) {
            return 'integer';
        } elseif (strpos($dbType, 'character varying') !== false || $dbType === 'varchar') {
            return 'string';
        } elseif (strpos($dbType, 'text') !== false) {
            return 'text';
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
