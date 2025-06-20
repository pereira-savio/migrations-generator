<?php

namespace Migrations\MigrationsGenerator\Files;

use Migrations\MigrationsGenerator\Message;
use Migrations\MigrationsGenerator\Services\BasePath;

class Seeds
{
    public function generate($tableName, $schemaFields): void
    {
        $seedFileName = date('Y_m_d_His').'_seed_'.$tableName.'.php';
        $seedPath = (new BasePath)->seedersPath($seedFileName);

        $content = "<?php\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n\n";
        $content .= "return new class extends Seeder {\n\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        DB::table('$tableName')->insert([\n";

        foreach ($schemaFields as $field) {
            $content .= "            [\n";
            foreach ($field as $key => $value) {
                if (is_null($value)) {
                    $valueStr = 'null';
                } else {
                    // Escapa as aspas simples eventuais no valor
                    $valueStr = "'".addslashes($value)."'";
                }
                $content .= "                '$key' => $valueStr,\n";
            }
            $content .= "            ],\n";
        }

        $content .= "        ]);\n";
        $content .= "    }\n";
        $content .= "};\n";

        try {
            file_put_contents($seedPath, $content);
            echo Message::success($seedFileName, 'Seed criado com sucesso:');
        } catch (\Exception $e) {
            echo Message::error('Erro ao criar a seed: '.$e->getMessage());
            exit;
        }
    }
}