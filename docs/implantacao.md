# Implantação em produção (DigitalOcean)

Passo a passo para colocar o OrcaMes no ar em um droplet novo, com HTTPS e backup.
Os comandos são executados no servidor, salvo quando indicado.

Em produção o sistema roda com `docker-compose.producao.yml`, que é diferente do ambiente de
desenvolvimento: o código vai dentro da imagem, o servidor web emite o certificado HTTPS sozinho,
o banco não fica exposto e os erros não mostram detalhes técnicos.

Para encurtar os comandos, defina este atalho. Ele vale só para a sessão atual do terminal:
ao abrir outra, defina de novo.

```sh
alias orca='docker compose -f docker-compose.producao.yml'
```

## 1. Criar o droplet

No painel da DigitalOcean:

- Imagem **Ubuntu 24.04 LTS**, com **2 GB de memória** ou mais.
- Autenticação por **chave SSH** (não por senha).
- Em *Networking → Firewalls*, crie um firewall liberando só as portas **22**, **80** e **443**
  (a 443 em TCP e UDP) e aplique ao droplet.
- Ative os **backups semanais** do droplet.

## 2. Preparar o servidor

```sh
ssh root@IP-DO-DROPLET
apt update && apt upgrade -y
curl -fsSL https://get.docker.com | sh
```

## 3. Baixar o projeto e configurar

```sh
git clone -b laravel https://github.com/tux-php/OrcaMes.git /opt/orcames
cd /opt/orcames
cp .env.producao.example .env
```

Preencha o `.env` para o primeiro teste, pelo IP e ainda sem domínio. Os comandos abaixo geram as
duas senhas do banco e a chave da aplicação:

```sh
IP=$(curl -4 -s ifconfig.me)
sed -i "s#^SERVER_NAME=.*#SERVER_NAME=:80#; s#^APP_URL=.*#APP_URL=http://$IP#; s#^SESSION_SECURE_COOKIE=.*#SESSION_SECURE_COOKIE=false#; s#^DB_PASSWORD=.*#DB_PASSWORD=$(openssl rand -hex 16)#; s#^DB_ROOT_PASSWORD=.*#DB_ROOT_PASSWORD=$(openssl rand -hex 16)#" .env

orca build
sed -i "s#^APP_KEY=.*#APP_KEY=$(orca run --rm --no-deps -T app php artisan key:generate --show --no-ansi 2>/dev/null)#" .env
grep -c '^APP_KEY=base64:' .env    # deve responder 1
```

A `APP_KEY` não pode mudar depois: guarde uma cópia do `.env` em lugar seguro, fora do servidor.

## 4. Subir e testar pelo IP

```sh
orca up -d
orca ps
orca logs app | tail -30
```

A primeira subida cria o banco e as tabelas. Abra `http://IP-DO-DROPLET/login`: a tela de login
deve aparecer. Ainda não há usuários; eles vêm da importação.

## 5. Virada dos dados

Faça no dia da troca, para não perder lançamentos:

1. Pare de usar o sistema antigo.
2. Exporte o banco do sistema antigo pelo phpMyAdmin (aba *Exportar*, formato SQL), que é o
   formato com que a importação foi testada. Pelo terminal, o equivalente é
   `mysqldump --complete-insert -u USUARIO -p orcamentos > orcamentos.sql`; sem `--complete-insert`
   o arquivo sai sem os nomes das colunas e a importação não o lê.
3. Leve o arquivo para o droplet novo (do seu computador):
   `scp orcamentos.sql root@IP-DO-DROPLET:/root/`
4. Importe:

   ```sh
   cd /opt/orcames
   orca cp /root/orcamentos.sql app:/tmp/orcamentos.sql
   orca exec app php artisan orcames:importar /tmp/orcamentos.sql --admin=SEU-EMAIL
   ```

5. Anote as **senhas provisórias** que o comando mostra; a troca é exigida no primeiro acesso.
6. Confira os totais por usuário mostrados pelo comando e apague o dump:
   `rm /root/orcamentos.sql && orca exec app rm /tmp/orcamentos.sql`

A importação só roda em banco vazio. Para refazer, apague tudo com `orca down -v` e volte ao passo 4.

## 6. Apontar o domínio e ligar o HTTPS

1. No DNS do domínio, crie um registro **A** apontando para o IP do droplet e aguarde propagar
   (`dig +short SEU-DOMINIO` deve responder com o IP).
2. No `.env`, troque as três linhas do teste por:

   ```
   SERVER_NAME=SEU-DOMINIO
   APP_URL=https://SEU-DOMINIO
   SESSION_SECURE_COOKIE=true
   ```

3. Aplique: `orca up -d`

O certificado é emitido na primeira visita (leva alguns segundos) e renovado automaticamente.
Se falhar, quase sempre é o DNS ainda não apontando para o droplet ou a porta 80/443 fechada no
firewall; veja `orca logs app`.

## 7. Backup diário

O script guarda o dump do banco e os comprovantes do IR em `/var/backups/orcames`, mantendo 14 dias:

```sh
/opt/orcames/scripts/backup.sh
ls -lh /var/backups/orcames
```

Agende para todo dia às 3h (`crontab -e`):

```
0 3 * * * /opt/orcames/scripts/backup.sh >> /var/log/orcames-backup.log 2>&1
```

Esses arquivos ficam no próprio droplet. Para sobreviver à perda do servidor, conte com os backups
do droplet (passo 1) ou copie a pasta periodicamente para outro lugar.

### Restaurar

```sh
cd /opt/orcames
gunzip -c /var/backups/orcames/banco_DATA.sql.gz | orca exec -T db sh -c 'mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"'
orca exec -T app tar -xzf - -C /app/storage/app < /var/backups/orcames/arquivos_DATA.tar.gz
```

## 8. Publicar uma versão nova

```sh
cd /opt/orcames
scripts/backup.sh
git pull
orca up -d --build
```

As migrations do banco rodam sozinhas na subida. Os dados, os comprovantes e os certificados
ficam em volumes do Docker e não são afetados.

## Cuidados

- **Nunca rode `orca down -v` em produção com dados**: o `-v` apaga o banco e os comprovantes.
- O `.env` do servidor contém as senhas e a `APP_KEY`; ele não vai para o git.
- Depois de alguns dias de uso sem problema, desligue o servidor antigo e troque as senhas que
  ele usava.
