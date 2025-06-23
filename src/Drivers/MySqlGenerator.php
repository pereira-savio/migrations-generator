<?php

namespace Migrations\MigrationsGenerator\Drivers;

use Illuminate\Support\Facades\DB;
use Migrations\MigrationsGenerator\Contracts\MigrationGeneratorInterface;
use Migrations\MigrationsGenerator\Files\Migrations;
use Migrations\MigrationsGenerator\Files\Seeds;
use Migrations\MigrationsGenerator\Message;
use Migrations\MigrationsGenerator\Services\SkipMigrationsTableFilter;

class MySqlGenerator implements MigrationGeneratorInterface
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
     * Gera as migrations para todas as tabelas do banco de dados MySQL.
     */
    public function migrations(): void
    {
        $this->generateMigrations();
    }

    /**
     * Gera as seeds para todas as tabelas do banco de dados MySQL.
     */
    public function seeds(): void
    {
        $this->generateSeeds();
    }

    /**
     * Encontra todas as tabelas do banco de dados atual, ignorando aquelas que devem ser puladas.
     *
     * @return array Lista de nomes das tabelas encontradas
     */
    public function findTables(): array
    {
        $tables = DB::connection()
            ->getDoctrineSchemaManager()
            ->listTableNames();
        $tableNames = [];

        foreach ($tables as $table) {
            // Pular tabelas que devem ser ignoradas
            if ($this->filter->shouldSkip($table)) {
                continue;
            }

            $tableNames[] = $table;
        }

        return $tableNames;
    }

    /**
     * Gera as migrations para cada tabela encontrada no banco de dados.
     */
    public function generateMigrations(): void
    {
        $tables = $this->findTables();

        foreach ($tables as $tableName) {

            echo Message::info($tableName, 'Gerando migration para a tabela:');

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

            $this->migrations->generate($tableName, $schemaFields);
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

            // Recupera os dados da tabela no MySQL
            $rows = DB::select("SELECT * FROM `$tableName`");
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
     * Mapeia o tipo de coluna do banco de dados para o tipo de coluna do Laravel.
     *
     * @param  string  $dbType  Tipo da coluna no banco de dados
     * @param  string  $driver  Nome do driver do banco de dados (mysql, mariadb, pgsql)
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