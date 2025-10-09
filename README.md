# 🏢 AroliStudio - Sistema de Gestão Empresarial

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Sistema completo de gestão empresarial desenvolvido em Laravel 10 com Livewire 3</strong>
</p>

## 📋 Sobre o Projeto

O **AroliStudio** é um sistema de gestão empresarial moderno que oferece funcionalidades completas para:

- 🏢 **Gestão de Empresas** - Cadastro e controle de empresas
- 👥 **Gestão de Clientes** - Controle completo de clientes
- 👨‍💼 **Gestão de Funcionários** - Administração de equipes
- 📦 **Gestão de Produtos** - Controle de estoque e produtos
- 🔧 **Gestão de Serviços** - Administração de serviços oferecidos
- 📋 **Pacotes Comerciais** - Criação de pacotes personalizados
- 💼 **Área Comercial** - Vendas, resumos e controle de consumo

## 🛠️ Stack Tecnológica

- **Backend**: Laravel 10, PHP 8.1+
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **UI Components**: Flowbite
- **Autenticação**: Laravel Jetstream + Fortify
- **Banco de Dados**: MySQL 8.0 (Docker)
- **Build Tool**: Vite
- **Validações**: CPF, CNPJ, datas brasileiras

## 🚀 Como Iniciar o Projeto

### 📋 Pré-requisitos

Certifique-se de ter instalado:

- ✅ **PHP 8.1 ou superior**
- ✅ **Composer**
- ✅ **Node.js e NPM**
- ✅ **Docker Desktop** (para o banco de dados)
- ✅ **Git**

### 🔧 Instalação Passo a Passo

#### **1. Clone o Repositório**
```bash
git clone <url-do-repositorio>
cd AroliStudio
```

#### **2. Instalar Dependências PHP**
```bash
composer install
```

#### **3. Instalar Dependências Node.js**
```bash
npm install
```

#### **4. Configurar Ambiente**

**Copie o arquivo de exemplo:**
```bash
copy .env.example .env
```

**Edite o arquivo `.env` com as seguintes configurações:**
```env
APP_NAME=AroliStudio
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Configurações do Banco de Dados (Docker MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aroli_studio
DB_USERNAME=aroli_user
DB_PASSWORD=aroli_password
```

#### **5. Configurar Banco de Dados**

**Opção A - Script Automático (Recomendado):**
```powershell
.\setup-database.ps1
```

**Opção B - Comandos Manuais:**
```bash
# Iniciar banco de dados
docker-compose up -d

# Gerar chave da aplicação
php artisan key:generate

# Executar todas as migrações
php artisan migrate:fresh

# Executar seeders (opcional)
php artisan db:seed
```

#### **6. Compilar Assets**
```bash
npm run dev
```

#### **7. Iniciar o Servidor**
```bash
php artisan serve
```

### 🌐 Acessar a Aplicação

Após seguir todos os passos, acesse:

- **URL Principal**: http://localhost:8000
- **Registro**: http://localhost:8000/register
- **Login**: http://localhost:8000/login

### 📱 Funcionalidades Principais

Após fazer login, você terá acesso a:

- **Dashboard**: `/dashboard` - Visão geral do sistema
- **Funcionários**: `/employee` - Gestão de funcionários
- **Clientes**: `/costumer` - Gestão de clientes
- **Produtos/Serviços**: `/negotiable` - Gestão de produtos e serviços
- **Comercial**: `/commercial` - Área comercial e vendas
- **Formulários**: `/form/*` - Formulários de cadastro

## 🐳 Gerenciamento do Docker

### **Comandos Úteis**

```bash
# Iniciar banco de dados
docker-compose up -d

# Parar banco de dados
docker-compose down

# Ver logs do MySQL
docker-compose logs mysql

# Acessar MySQL via linha de comando
docker exec -it aroli_mysql mysql -u root -p
```

### **Configurações do Container**

- **Imagem**: MySQL 8.0
- **Porta**: 3306
- **Banco**: aroli_studio
- **Usuário**: aroli_user
- **Senha**: aroli_password

## 🔧 Comandos de Desenvolvimento

```bash
# Setup completo do banco (recomendado)
.\setup-database.ps1

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Recriar banco (CUIDADO: apaga todos os dados)
php artisan migrate:fresh --seed

# Compilar assets para produção
npm run build

# Executar testes
php artisan test
```

## 🚨 Troubleshooting

### **Erro de Conexão com Banco**
- Verifique se o Docker está rodando: `docker ps`
- Confirme as configurações no `.env`
- Aguarde alguns segundos após iniciar o container

### **Erro "MySQL server has gone away"**
- O banco de dados não está rodando
- Execute: `docker-compose up -d`
- Verifique se a porta 3306 não está ocupada

### **Erro de Permissões (Windows)**
- Execute o PowerShell como Administrador
- Verifique se o Docker Desktop está rodando

### **Assets não carregam**
- Execute: `npm run dev`
- Limpe o cache: `php artisan view:clear`

### **Migrações falham**
- Execute o script automático: `.\setup-database.ps1`
- Verifique se o banco está rodando: `docker ps`
- Confirme as credenciais no `.env`
- Execute: `php artisan migrate:status`

### **Erro "Undefined variable $name"**
- Limpe o cache de views: `php artisan view:clear`
- Os componentes modal foram corrigidos automaticamente

## 📁 Estrutura do Projeto

```
AroliStudio/
├── app/
│   ├── Livewire/          # Componentes Livewire
│   ├── Models/            # Modelos Eloquent
│   ├── Http/Controllers/  # Controladores
│   └── Casts/             # Casts personalizados
├── database/
│   ├── migrations/        # Migrações do banco
│   └── seeders/          # Seeders para dados iniciais
├── resources/
│   ├── views/            # Views Blade
│   └── js/               # Assets JavaScript
├── docker/               # Configurações Docker
└── routes/               # Definição de rotas
```

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 📞 Suporte

Se encontrar problemas ou tiver dúvidas:

1. Verifique a seção [Troubleshooting](#-troubleshooting)
2. Consulte a documentação do [Docker](docker/README.md)
3. Abra uma issue no repositório

---

**Desenvolvido com ❤️ usando Laravel e Livewire**