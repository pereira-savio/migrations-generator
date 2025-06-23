<?php

namespace Migrations\MigrationsGenerator\Files;

use Migrations\MigrationsGenerator\Message;
use Migrations\MigrationsGenerator\Services\BasePath;

class Migrations
{
    /**
     * Gera o arquivo de migration para a criação de uma tabela.
     *
     * @param  string  $tableName  Nome da tabela a ser criada
     * @param  string  $schemaFields  Campos da tabela em formato de string
     */
    public function generate($tableName, $schemaFields): void
    {
        $migrationFileName = '0000_00_00_'.date('His').'_create_'.$tableName.'_table.php';
        $migrationPath = (new BasePath)->migrationsPath($migrationFileName);

        $content = "<?php\n\n";
        $content .= "use Illuminate\\Database\\Migrations\\Migration;\n";
        $content .= "use Illuminate\\Database\\Schema\\Blueprint;\n";
        $content .= "use Illuminate\\Support\\Facades\\Schema;\n\n";
        $content .= "return new class extends Migration {\n";
        $content .= "public function up()\n";
        $content .= "    {\n";
        $content .= "        Schema::create('$tableName', function (Blueprint \$table) {\n";
        $content .= "            $schemaFields\n";
        $content .= "            \$table->timestamps();\n";
        $content .= "        });\n";
        $content .= "    }\n";
        $content .= "\n\n";
        $content .= "    public function down()\n";
        $content .= "    {\n";
        $content .= "        Schema::dropIfExists('$tableName');\n";
        $content .= "    }\n";
        $content .= '};';

        try {
            file_put_contents($migrationPath, $content);
            echo Message::success($migrationFileName, 'Migration criada com sucesso:');
        } catch (\Exception $e) {
            echo Message::error('Erro ao criar a migration: '.$e->getMessage());
            exit;
        }
    }
}