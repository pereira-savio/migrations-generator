<?php
namespace Migrations\MigrationsGenerator\Services;

use Migrations\MigrationsGenerator\Message;

class GenerateMigrations
{
    /**
     * Gera o arquivo de migration para a criação de uma tabela.
     *
     * @param string $tableName Nome da tabela a ser criada
     * @param string $schemaFields Campos da tabela em formato de string
     * @return void
     */
    public function generate($tableName, $schemaFields): void
    {
        $migrationFileName = date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
        $migrationPath = (new BasePath)->file($migrationFileName);

        $content = "<?php

use Illuminate\\Database\\Migrations\\Migration;
use Migrations\MigrationsGenerator\Message;

use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    /**
     * Executa as migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('$tableName', function (Blueprint \$table) {
            $schemaFields
            \$table->timestamps();
        });
    }

    /**
     * Reverte as migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('$tableName');
    }
};";

        try {
            file_put_contents($migrationPath, $content);
            echo Message::success($migrationFileName, "Migration criada com sucesso:");
        } catch (\Exception $e) {
            echo Message::error("Erro ao criar a migration: " . $e->getMessage());
            die;
        }
    }

}