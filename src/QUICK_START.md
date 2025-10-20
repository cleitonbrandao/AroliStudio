# ⚡ Guia Rápido - AroliStudio

## 🚀 Inicialização Rápida (5 minutos)

### **1. Pré-requisitos**
- ✅ PHP 8.1+, Composer, Node.js, Docker Desktop

### **2. Comandos Essenciais**
```bash
# 1. Instalar dependências
composer install && npm install

# 2. Configurar ambiente
copy .env.example .env

# 3. Setup completo do banco (automático!)
.\setup-database.ps1

# 4. Compilar e iniciar
npm run dev
php artisan serve
```

### **3. Acessar**
- **URL**: http://localhost:8000
- **Registro**: http://localhost:8000/register

### **4. Configuração do .env**
```env
APP_NAME=AroliStudio
DB_DATABASE=aroli_studio
DB_USERNAME=aroli_user
DB_PASSWORD=aroli_password
```

### **5. Comandos Úteis**
```bash
# Setup completo do banco
.\setup-database.ps1

# Parar banco
docker-compose down

# Limpar cache
php artisan cache:clear

# Ver logs do banco
docker-compose logs mysql
```

## 🆘 Problemas Comuns

| Erro | Solução |
|------|---------|
| `MySQL server has gone away` | Execute `.\setup-database.ps1` |
| `Connection refused` | Verifique se Docker está rodando |
| `Assets não carregam` | Execute `npm run dev` |
| `Migrações falham` | Execute `.\setup-database.ps1` |
| `Undefined variable $name` | Execute `php artisan view:clear` |

---
**Para instruções detalhadas, consulte o [README.md](README.md)**
