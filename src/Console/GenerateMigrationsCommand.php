<?php

namespace Migrations\MigrationsGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateMigrationsCommand extends Command
{
    protected $signature = 'generate:migrations';
    protected $description = 'Gera arquivos de migrations automaticamente a partir do banco de dados atual (MySQL ou PostgreSQL)';

    public function handle()
    {
        $this->info("Iniciando a geração de migrations...");

        // Detecta o driver de conexão (mysql ou pgsql)
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
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

                // Recupera as colunas da tabela no MySQL
                $columns = DB::select("SHOW COLUMNS FROM `$tableName`");
                $schemaFields = "";

                foreach ($columns as $column) {
                    // Propriedades da coluna: Field, Type, Null, Key, Default, Extra
                    $field = $column->Field;
                    $type = $this->mapColumnType($column->Type, $driver);
                    $nullable = ($column->Null === "YES") ? "->nullable()" : "";
                    $default = ($column->Default !== null) ? "->default('" . addslashes($column->Default) . "')" : "";

                    // Se a coluna for primary key auto-increment, use o método increments
                    if ($column->Key === 'PRI' && strpos($column->Extra, 'auto_increment') !== false) {
                        $schemaFields .= "\$table->increments('$field');\n\t\t\t";
                    } else {
                        $schemaFields .= "\$table->$type('$field')$nullable$default;\n\t\t\t";
                    }
                }

                $this->criaMigration($tableName, $schemaFields);
                // Aguarda 1 segundo para garantir timestamps únicos
                sleep(1);
            }
        } elseif ($driver === 'pgsql') {
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type='BASE TABLE'");

            foreach ($tables as $table) {
                $tableName = $table->table_name;

                // Pular a tabela de migrations
                if ($tableName === 'migrations') {
                    continue;
                }

                $this->info("Gerando migration para a tabela: $tableName");

                // Recupera as colunas da tabela no PostgreSQL
                $columns = DB::select(
                    "SELECT column_name, data_type, is_nullable, column_default
                     FROM information_schema.columns
                     WHERE table_schema = 'public' AND table_name = ?",
                     [$tableName]
                );
                $schemaFields = "";

                foreach ($columns as $column) {
                    // Propriedades da coluna: column_name, data_type, is_nullable, column_default
                    $field = $column->column_name;
                    $type = $this->mapColumnType($column->data_type, $driver);
                    $nullable = ($column->is_nullable === "YES") ? "->nullable()" : "";
                    $default = ($column->column_default !== null) ? "->default('" . addslashes($column->column_default) . "')" : "";

                    // Verifica se a coluna é serial (auto incremento) verificando 'nextval' na coluna default
                    if ($column->column_default !== null && strpos($column->column_default, 'nextval') !== false) {
                        $schemaFields .= "\$table->increments('$field');\n\t\t\t";
                    } else {
                        $schemaFields .= "\$table->$type('$field')$nullable$default;\n\t\t\t";
                    }
                }

                $this->criaMigration($tableName, $schemaFields);
                // Aguarda 1 segundo para garantir timestamps únicos
                sleep(1);
            }
        } else {
            $this->error("Driver '$driver' não suportado.");
            return;
        }

        $this->info("Geração de migrations concluída!");
    }

    /**
     * Cria o arquivo de migration com o conteúdo fornecido.
     *
     * @param string $tableName
     * @param string $schemaFields
     * @return void
     */
    protected function criaMigration($tableName, $schemaFields)
    {
        $migrationClassName = 'Create' . Str::studly($tableName) . 'Table';
        $migrationFileName = date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
        $migrationPath = base_path('database/migrations/' . $migrationFileName);

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
    }

    /**
     * Mapeia o tipo da coluna do banco para o método de migration do Laravel.
     *
     * @param string $dbType
     * @param string $driver
     * @return string
     */
    protected function mapColumnType($dbType, $driver)
    {
        if ($driver === 'mysql') {
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
        } elseif ($driver === 'pgsql') {
            // Para PostgreSQL
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
        } else {
            return 'string';
        }
    }
}