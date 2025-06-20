<?php

namespace Migrations\MigrationsGenerator\Services;

use Migrations\MigrationsGenerator\Contracts\MigrationGeneratorInterface;
use Migrations\MigrationsGenerator\Drivers\MariaDBGenerator;
use Migrations\MigrationsGenerator\Drivers\MySqlGenerator;
use Migrations\MigrationsGenerator\Drivers\PostgresGenerator;

class DriverSelector
{
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
