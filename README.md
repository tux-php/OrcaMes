# OrcaMes

Controle orçamentário familiar: contas do mês (cartão, escola, babá…), receitas e o que sobra.

Laravel 13, PHP 8.4 e MySQL 8.4, rodando em Docker. O sistema antigo (PHP puro) está em `legacy/`
apenas para consulta.

## Subir o ambiente

```sh
cp .env.example .env          # preencha DB_PASSWORD e DB_ROOT_PASSWORD
docker compose build
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose up -d
docker compose exec app php artisan migrate
```

O sistema fica em http://localhost:8080.

## Importar os dados do sistema antigo

Copie o dump para `storage/app/private/` (pasta ignorada pelo git) e rode, em um banco vazio:

```sh
docker compose exec app php artisan orcames:importar storage/app/private/orcamentos.sql --admin=seu@email
```

O comando mostra uma conferência por usuário e as senhas provisórias, que precisam ser trocadas
no primeiro acesso. As regras da importação estão descritas em `app/Importacao/MapeadorLegado.php`.

O sistema antigo não guardava o salário de cada mês, então o histórico chega sem receitas. Para
repetir o salário mais recente nos meses anteriores que têm lançamentos (as receitas criadas
ficam marcadas como "(estimado)"):

```sh
docker compose exec app php artisan orcames:preencher-salarios            # só mostra
docker compose exec app php artisan orcames:preencher-salarios --aplicar  # grava
```

## Testes

```sh
docker compose exec app php artisan test
```

## Produção

O ambiente acima é de desenvolvimento. Para colocar no ar (droplet, HTTPS, importação e backup),
siga [docs/implantacao.md](docs/implantacao.md).

## Como o sistema pensa

- **Conta**: o que se paga (ex.: "Escola"). Pertence a uma pessoa e pode ser recorrente.
- **Lançamento**: o valor de uma conta em um mês de referência; fica em aberto até ser pago.
- **Mês de referência**: o mês do salário que paga a conta.
- **Gerar mês**: cria os lançamentos das contas recorrentes e o salário que ainda faltam no mês,
  repetindo os últimos valores. Pode ser usado mais de uma vez.
- **Perfis**: o membro vê só o que é dele; o administrador vê tudo e gerencia usuários e categorias.
