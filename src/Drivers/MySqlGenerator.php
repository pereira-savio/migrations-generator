<?php

namespace Migrations\MigrationsGenerator\Drivers;

use Illuminate\Support\Facades\DB;
use Migrations\MigrationsGenerator\Contracts\MigrationGeneratorInterface;
use Migrations\MigrationsGenerator\Message;
use Migrations\MigrationsGenerator\Services\GenerateMigrations;
use Migrations\MigrationsGenerator\Services\SkipMigrationsTableFilter;

class MySqlGenerator implements MigrationGeneratorInterface
{
    protected SkipMigrationsTableFilter $filter;

    protected GenerateMigrations $migration;

    public function __construct()
    {
        $this->filter = new SkipMigrationsTableFilter;
        $this->migration = new GenerateMigrations;
    }

    /**
     * Gera as migrations para todas as tabelas do banco de dados MySQL.
     *
     * @return void
     */
    public function generate(): void
    {
        $tables = DB::connection()
            ->getDoctrineSchemaManager()
            ->listTableNames();

        foreach ($tables as $tableName) {

            // Pular tabelas que devem ser ignoradas
            if ($this->filter->shouldSkip($tableName)) {
                continue;
            }

            echo Message::info($tableName, "Gerando migration para a tabela:");

            // Recupera as colunas da tabela no MySQL
            $columns = DB::select("SHOW COLUMNS FROM `$tableName`");
            $schemaFields = '';

            foreach ($columns as $column) {
                // Propriedades da coluna: Field, Type, Null, Key, Default, Extra
                $field = $column->Field;
                $type = $this->mapColumnType($column->Type, 'mysql');
                $nullable = ($column->Null === 'YES') ? '->nullable()' : '';
                $default = ($column->Default !== null) ? "->default('".addslashes($column->Default)."')" : '';

                // Se a coluna for primary key auto-increment, use o método increments
                if ($column->Key === 'PRI' && strpos($column->Extra, 'auto_increment') !== false) {
                    $schemaFields .= "\$table->increments('$field');\n\t\t\t";
                } else {
                    $schemaFields .= "\$table->$type('$field')$nullable$default;\n\t\t\t";
                }
            }

            $this->migration->generate($tableName, $schemaFields);
            sleep(1);
        }
    }

    /**
     * Mapeia o tipo de coluna do banco de dados para o tipo de coluna do Laravel.
     *
     * @param string $dbType Tipo da coluna no banco de dados
     * @param string $driver Nome do driver do banco de dados (mysql, mariadb, pgsql)
     * @return string Tipo de coluna correspondente no Laravel
     */
    public function mapColumnType(string $dbType, string $driver): string
    {
        if (preg_match('/^int/', $dbType)) {
            return 'integer';
        } elseif (preg_match('/^varchar/', $dbType)) {
            return 'string';
        } elseif (preg_match('/^text/', $dbType)) {
            return 'text';
        } elseif (preg_match('/^datetime/', $dbType)) {
            return 'dateTime';
        } elseif (preg_match('/^date/', $dbType)) {
            return 'date';
        } elseif (preg_match('/^decimal/', $dbType)) {
            return 'decimal';
        } else {
            return 'string';
        }
    }
}