# Docker MySQL para AroliStudio

## Configuração do Banco de Dados

Este Docker Compose configura apenas o banco de dados MySQL para o projeto AroliStudio.

### Configurações do Container

- **Imagem**: MySQL 8.0
- **Porta**: 3306
- **Banco de dados**: aroli_studio
- **Usuário root**: root / root
- **Usuário da aplicação**: aroli_user / aroli_password

### Como usar

#### Opção 1: Scripts PowerShell (Recomendado para Windows)

1. **Iniciar o banco de dados:**
```powershell
.\docker\start-database.ps1
```

2. **Parar o banco de dados:**
```powershell
.\docker\stop-database.ps1
```

#### Opção 2: Comandos Docker diretos

1. **Iniciar o banco de dados:**
```bash
docker-compose up -d
```

2. **Parar o banco de dados:**
```bash
docker-compose down
```

3. **Ver logs do container:**
```bash
docker-compose logs mysql
```

4. **Acessar o MySQL via linha de comando:**
```bash
docker exec -it aroli_mysql mysql -u root -p
```

### Configuração no .env

**IMPORTANTE**: Atualize seu arquivo `.env` com as seguintes configurações:

```env
APP_NAME=AroliStudio
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aroli_studio
DB_USERNAME=aroli_user
DB_PASSWORD=aroli_password
```

**Ou use o arquivo de exemplo**: `docker/env-docker-example.txt`

### Volumes

- **mysql_data**: Persiste os dados do MySQL entre reinicializações do container
- **docker/mysql/init**: Scripts de inicialização executados na primeira criação

### Rede

O container usa a rede `aroli_network` para isolamento e futuras expansões.

### Próximos Passos

Após iniciar o banco de dados:

1. **Atualize seu arquivo .env** com as configurações do Docker
2. **Gere a chave da aplicação:**
   ```bash
   php artisan key:generate
   ```
3. **Execute as migrações:**
   ```bash
   php artisan migrate
   ```
4. **Execute os seeders (opcional):**
   ```bash
   php artisan db:seed
   ```
5. **Inicie o servidor Laravel:**
   ```bash
   php artisan serve
   ```

### Troubleshooting

- **Erro de conexão**: Verifique se o Docker está rodando e o container está ativo
- **Porta ocupada**: Se a porta 3306 estiver em uso, altere no docker-compose.yml
- **Permissões**: No Windows, execute o PowerShell como Administrador se necessário
