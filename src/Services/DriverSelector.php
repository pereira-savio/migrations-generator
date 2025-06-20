<?php

namespace Migrations\MigrationsGenerator\Services;

use Migrations\MigrationsGenerator\Contracts\MigrationGeneratorInterface;
use Migrations\MigrationsGenerator\Drivers\MariaDBGenerator;
use Migrations\MigrationsGenerator\Drivers\MySqlGenerator;
use Migrations\MigrationsGenerator\Drivers\PostgresGenerator;

class DriverSelector
{
    /**
     * Seleciona o gerador de migrations baseado no driver do banco de dados.
     *
     * @param string $driver Nome do driver do banco de dados (mysql, mariadb, pgsql)
     * @return MigrationGeneratorInterface Instância do gerador de migrations correspondente
     * @throws \InvalidArgumentException Se o driver não for suportado
     */
    public function select(string $driver): MigrationGeneratorInterface
    {
        return match ($driver) {
            'mysql' => new MySqlGenerator,
            'mariadb' => new MariaDBGenerator,
            'pgsql' => new PostgresGenerator,
            default => throw new \InvalidArgumentException("Driver inválido: $driver"),
        };
    }
}