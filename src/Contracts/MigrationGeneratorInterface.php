<?php

namespace Migrations\MigrationsGenerator\Contracts;

interface MigrationGeneratorInterface
{
    public function generate(): void;
    public function mapColumnType(string $dbType, string $driver): string;
}