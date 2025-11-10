# Guia de Instalação - Marketplace PHP + MySQL

## ⚡ Instalação Rápida (5 minutos)

### 1️⃣ Extrair o Arquivo

```bash
unzip marketplace-php.zip
cd marketplace
```

### 2️⃣ Criar o Banco de Dados

Abra o terminal e acesse o MySQL:

```bash
mysql -u root -p
```

Cole os comandos:

```sql
CREATE DATABASE marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE marketplace;
```

Depois execute o arquivo SQL:

```bash
mysql -u root -p marketplace < database.sql
```

### 3️⃣ Configurar Conexão (Opcional)

Se seu MySQL tem usuário/senha diferentes, edite `config/database.php`:

```php
$servername = "localhost";
$username = "seu_usuario";    // ← Altere aqui
$password = "sua_senha";      // ← Altere aqui
$dbname = "marketplace";
```

### 4️⃣ Colocar no Servidor Web

**XAMPP (Windows):**
```
C:\xampp\htdocs\marketplace
```

**XAMPP (Mac/Linux):**
```
/Applications/XAMPP/xamppfiles/htdocs/marketplace
```

**WAMP (Windows):**
```
C:\wamp64\www\marketplace
```

**Nginx/Linux:**
```
/var/www/html/marketplace
```

### 5️⃣ Acessar no Navegador

```
http://localhost/marketplace
```

## 🧪 Teste Rápido

### Criar Conta de Teste

1. Clique em **"Cadastro"**
2. Preencha:
   - Nome: `João Silva`
   - Email: `joao@test.com`
   - Senha: `senha123`
   - Tipo: `Comprador`
3. Clique em **"Cadastrar"**

### Fazer Login

1. Clique em **"Entrar"**
2. Email: `joao@test.com`
3. Senha: `senha123`
4. Clique em **"Entrar"**

### Testar Compra

1. Clique em um produto
2. Clique em **"Adicionar ao Carrinho"**
3. Vá em **"Carrinho"**
4. Clique em **"Finalizar Compra"**
5. Veja em **"Meus Pedidos"**

### Virar Admin

1. Abra o MySQL:
```bash
mysql -u root -p marketplace
```

2. Execute:
```sql
UPDATE users SET role='admin' WHERE email='joao@test.com';
```

3. Faça login novamente
4. Você verá "Categorias" e "Pedidos" no menu

## 📁 Estrutura

```
marketplace/
├── config/database.php       ← Configuração do banco
├── includes/                 ← Funções e templates
├── css/style.css            ← Estilos
├── js/script.js             ← JavaScript
├── index.php                ← Home
├── login.php                ← Login
├── register.php             ← Registro
├── product-detail.php       ← Detalhes do produto
├── cart.php                 ← Carrinho
├── my-products.php          ← Meus produtos (vendedor)
├── my-orders.php            ← Meus pedidos
├── admin-categories.php     ← Categorias (admin)
├── admin-orders.php         ← Pedidos (admin)
├── database.sql             ← Script SQL
└── README.md                ← Documentação
```

## 🔧 Requisitos

- **PHP 7.4+** (recomendado: 8.0+)
- **MySQL 5.7+**
- **Servidor Web** (Apache, Nginx, etc.)
- **Navegador** moderno

## ✅ Funcionalidades

### Todos
- ✅ Ver todos os produtos
- ✅ Buscar produtos
- ✅ Filtrar por categoria

### Compradores
- ✅ Adicionar ao carrinho
- ✅ Finalizar compra
- ✅ Ver pedidos

### Vendedores
- ✅ Cadastrar produtos
- ✅ Editar produtos
- ✅ Deletar produtos

### Admin
- ✅ Gerenciar categorias
- ✅ Gerenciar pedidos
- ✅ Atualizar status

## 🔐 Segurança

- ✅ Hash de senhas (BCRYPT)
- ✅ Proteção SQL Injection
- ✅ Proteção XSS
- ✅ Validação de entrada
- ✅ Controle de acesso

## 🐛 Problemas Comuns

### Erro: "Erro de conexão"
```bash
# Verifique se MySQL está rodando
mysql -u root -p
```

### Erro: "Table doesn't exist"
```bash
# Execute o SQL novamente
mysql -u root -p marketplace < database.sql
```

### Página em branco
- Ative `display_errors` em `php.ini`
- Verifique os logs do servidor

### Carrinho não funciona
- Verifique se JavaScript está ativado
- Limpe o cache do navegador

## 📞 Suporte

Veja o arquivo `README.md` para documentação completa.

---

**Pronto! Seu marketplace está funcionando! 🎉**
