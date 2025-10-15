# Guia de Configuração do Projeto

## Visão Geral
Este projeto é uma aplicação Laravel que utiliza Docker para gerenciamento de ambiente. Ele inclui serviços como MySQL, Redis, e PHP-FPM configurados para rodar em contêineres Docker.

## Configuração Inicial

### Requisitos
Certifique-se de ter os seguintes softwares instalados:
- Docker (versão mínima recomendada: 20.10)
- Docker Compose (versão mínima recomendada: 1.29)

### Configuração do Host
Adicione a seguinte entrada ao arquivo `hosts` do seu sistema operacional para mapear o domínio local:

**Linux:** `/etc/hosts`

**Windows:** `C:/Windows/System32/drivers/etc/hosts`

```
127.0.0.1   raconsultoria.com.br
```

### Configuração de Arquivos de Ambiente
1. Copie os arquivos de exemplo `.env` para criar os arquivos de configuração necessários:
   ```
   cp .env_sample .env
   ```
2. Edite o arquivo `.env` para configurar as variáveis de ambiente, como credenciais de banco de dados, chaves de API, e outras configurações específicas do projeto.

### Login no GitHub Container Registry
Antes de buildar as imagens Docker, faça login no GitHub Container Registry:
```powershell
 echo <token do git> | docker login ghcr.io -u <user do git> --password-stdin
```
Substitua `<token do git>` pelo seu token de acesso do GitHub e `<user do git>` pelo seu usuário do GitHub.

### Subindo os Contêineres
Para iniciar os serviços Docker, execute os seguintes comandos:

1. Construa as imagens Docker:
   ```
   docker-compose build --no-cache
   ```
2. Inicie os contêineres em segundo plano:
   ```
   docker-compose up -d
   ```
3. Verifique se os contêineres estão rodando:
   ```
   docker-compose ps
   ```

## Comandos Úteis

### Limpeza e Recriação
Se precisar limpar e recriar os contêineres, use os comandos:

1. Parar e remover os contêineres:
   ```
   docker-compose down
   ```
2. Remover volumes associados:
   ```
   docker-compose down -v
   ```
3. Subir novamente os contêineres:
   ```
   docker-compose up -d
   ```

### Logs
Para visualizar os logs dos serviços:
```bash
   docker-compose logs -f
```

## Estrutura do Projeto

### Diretórios Principais
- **docker/**: Contém configurações para os serviços Docker, como MySQL, Nginx e PHP-FPM.
- **src/**: Código-fonte da aplicação Laravel.
- **config/**: Arquivos de configuração do Laravel.
- **database/**: Migrações, seeders e banco de dados SQLite (se aplicável).
- **public/**: Arquivos públicos, como `index.php` e assets.
- **resources/**: Arquivos de frontend, como views Blade, CSS e JavaScript.
- **routes/**: Arquivos de rotas da aplicação.
- **tests/**: Testes unitários e de funcionalidade.

### Configuração Adicional
- Certifique-se de configurar corretamente os certificados SSL no diretório `docker/nginx/ssl/` para habilitar HTTPS.
- Caso o diretório `docker/nginx/ssl/` esteja vazio, um certificado autoassinado será gerado automaticamente.
- Utilize o arq