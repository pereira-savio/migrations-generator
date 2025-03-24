<?php

namespace Migrations\MigrationsGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateMigrationsCommand extends Command
{
    protected $signature = 'generate:migrations';
    protected $description = 'Gera arquivos de migrations automaticamente a partir do banco de dados atual';

    public function handle()
    {
        $this->info("Iniciando a geração de migrations...");

        // Obter o nome do banco de dados configurado
        $databaseName = DB::getDatabaseName();
        $tables = DB::select("SHOW TABLES");
        $tablesKey = "Tables_in_$databaseName";

        foreach ($tables as $table) {
            $tableName = $table->$tablesKey;

            // Pular a tabela de migrations
            if ($tableName === 'migrations') {
                continue;
            }

            $this->info("Gerando migration para a tabela: $tableName");

            // Recupera as colunas da tabela
            $columns = DB::select("SHOW COLUMNS FROM `$tableName`");

            // Define o nome da classe e do arquivo de migration
            $migrationClassName = 'Create' . Str::studly($tableName) . 'Table';
            $migrationFileName = date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
            $migrationPath = base_path('database/migrations/' . $migrationFileName);

            // Monta as linhas de código para cada coluna
            $schemaFields = "";
            foreach ($columns as $column) {
                // Propriedades da coluna: Field, Type, Null, Key, Default, Extra
                $field = $column->Field;
                $type = $this->mapColumnType($column->Type);
                $nullable = ($column->Null === "YES") ? "->nullable()" : "";
                $default = ($column->Default !== null) ? "->default('" . addslashes($column->Default) . "')" : "";

                // Se a coluna for primary key auto-increment, use o método increments
                if ($column->Key === 'PRI' && strpos($column->Extra, 'auto_increment') !== false) {
                    $schemaFields .= "\$table->increments('$field');\n\t\t\t";
                } else {
                    $schemaFields .= "\$table->$type('$field')$nullable$default;\n\t\t\t";
                }
            }

            // Cria o conteúdo do arquivo de migration
            $content = "<?php

use Illuminate\\Database\\Migrations\\Migration;
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
};
";

            file_put_contents($migrationPath, $content);
            $this->info("Migration gerada: $migrationFileName");

            // Aguarda 1 segundo para garantir timestamps únicos
            sleep(1);
        }

        $this->info("Geração de migrations concluída!");
    }

    /**
     * Mapeia o tipo da coluna do banco para o método de migration do Laravel.
     *
     * @param string $dbType
     * @return string
     */
    protected function mapColumnType($dbType)
    {
        // Mapeamento básico; adicione mais condições conforme necessário
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
            return 'string'; // Valor padrão
        }
    }
}