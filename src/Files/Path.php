<?php

namespace Migrations\MigrationsGenerator\Files;

class Path
{
    /**
     * Retorna o caminho completo do diretório de migrations.
     *
     * @param  string  $path  Caminho do diretório de migrations
     * @return string Caminho completo do diretório
     */
    public function databasePath(string $path): string
    {
        return base_path("database/$path");
    }

    /**
     * Valida se o diretório existe, caso contrário, cria.
     *
     * @param  string  $path  Caminho do diretório a ser validado
     *
     * @throws \Exception Se não for possível criar o diretório
     */
    public function validatePath(string $path): void
    {
        if (! $this->existDirPath($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Verifica se o diretório existe.
     *
     * @param  string  $path  Caminho do diretório
     * @return bool Retorna true se o diretório existir, caso contrário, false
     */
    public function existDirPath(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Valida se o arquivo DatabaseSeeder.php existe, caso contrário, cria.
     *
     * @param  string  $path  Caminho do diretório onde o arquivo deve existir
     *
     * @throws \Exception Se não for possível criar o arquivo
     */
    public function validateFile(string $path, array $seeders): void
    {
        if (! $this->existFile($path)) {
            $this->createDatabaseSeeder($seeders);
        }
    }

    /**
     * Verifica se o arquivo DatabaseSeeder.php existe no caminho especificado.
     *
     * @param  string  $path  Caminho do diretório onde o arquivo deve existir
     * @return bool Retorna true se o arquivo existir, caso contrário, false
     */
    public function existFile(string $path): bool
    {
        return file_exists("$path/DatabaseSeeder.php");
    }

    /**
     * Cria o arquivo DatabaseSeeder.php com o conteúdo padrão.
     *
     * @throws \Exception Se não for possível criar o arquivo
     */
    public function createDatabaseSeeder(array $seeders): void
    {
        $basePath = $this->databasePath('seeders/');

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use Illuminate\\Support\\Facades\\File;\n\n";
        $content .= "class NewDatabaseSeeder extends Seeder {\n\n";
        $content .= "    public function run(): array\n";
        $content .= "    {\n";
        $content .= "        \$seederPath = database_path('seeders/New/');\n";
        $content .= "        \$files = File::files(\$seederPath);\n";
        $content .= "        \$seeds = [];\n\n";

        $content .= "        foreach (\$files as \$file) {\n";
        $content .= "            \$class = pathinfo(\$file, PATHINFO_FILENAME);\n";
        $content .= "            \$seeds[] = \"Database\\Seeders\\New\\\\\$class\";\n";
        $content .= "        }\n";

        $content .= "        return \$seeds;\n";

        $content .= "    }\n";
        $content .= '}';

        file_put_contents($basePath.'NewDatabaseSeeder.php', $content);
    }
}
