# migrations-generator

Pacote Laravel 12 para gerar automaticamente arquivos de migrations a partir de um banco de dados MySQL ou PostgreSQL.

## Requisitos

- PHP >= 7.2
- Laravel 8, 9, 10, 11 ou 12

## Instalação

1. No seu projeto Laravel, execute:

   ```bash
   composer require saviorenato/migrations-generator --dev
   ```

2. O pacote utiliza autodiscovery do Laravel; o service provider será registrado automaticamente.

> **Observação:** se preferir instalar como dependência de produção, remova o `--dev`.

## Uso

Para gerar as migrations com base na estrutura atual do banco, basta executar o comando:

```bash
php artisan generate:migrations
```

Isso irá:

- Ler todas as tabelas do banco (exceto a tabela padrão `migrations`).
- Criar, em `database/migrations`, arquivos de migrations nomeados no formato `YYYY_MM_DD_HHMMSS_create_{nome_tabela}_table.php`.
- Cada migration gerada conterá:
  - Método `up()` com `Schema::create('{tabela}', …)` para recriar a tabela.
  - Método `down()` com `Schema::dropIfExists('{tabela}')` para excluir a tabela.

## Personalização

Se quiser ajustar algum detalhe, edite o comando em:

```
src/Console/GenerateMigrationsCommand.php
```

## Limitações

- Não gera índices, chaves estrangeiras, triggers ou constraints.
- Não suporta colunas compostas (composite keys) ou tipos avançados de colunas (JSON, enums, etc.) automaticamente.

## Contribuições

1. Faça um fork deste repositório.
2. Crie uma branch para sua feature ou correção:

   ```bash
   ```

git checkout -b feature-nova

````
3. Faça commit das suas alterações:
   ```bash
git commit -m "Descrição da sua contribuição"
````

4. Envie um pull request para análise.

## Licença

Este projeto está licenciado sob a licença MIT. Consulte o arquivo [LICENSE.txt](LICENSE.txt) para mais detalhes.

## Autor

Sávio Pereira ([saviorenato@gmail.com](mailto\:saviorenato@gmail.com))
