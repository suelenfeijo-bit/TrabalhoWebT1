# Marketplace de Produtos - PHP + MySQL

Um sistema web completo de marketplace desenvolvido com **HTML, CSS, JavaScript puro** no frontend e **PHP** no backend com banco de dados **MySQL**.

## 📋 Requisitos do Projeto

Este projeto atende todos os requisitos especificados:

- ✅ **Front-end:** HTML5, CSS3, JavaScript puro (sem frameworks)
- ✅ **Back-end:** PHP puro (sem frameworks)
- ✅ **Banco de Dados:** MySQL
- ✅ **Autenticação:** Login e registro de usuários com hash de senha
- ✅ **CRUD Completo:** Produtos, categorias e pedidos
- ✅ **Validação:** Frontend e backend
- ✅ **Segurança:** Proteção contra SQL Injection, XSS, hash de senhas
- ✅ **Responsividade:** Design mobile-first
- ✅ **Documentação:** Completa com instruções

## 🚀 Instalação Rápida

### Pré-requisitos

- PHP 7.4+ (recomendado: 8.0+)
- MySQL 5.7+
- Servidor web (Apache, Nginx, etc.)
- Navegador moderno

### Passo 1: Preparar o Banco de Dados

1. Abra o MySQL:
```bash
mysql -u root -p
```

2. Execute o script SQL:
```sql
CREATE DATABASE marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE marketplace;
```

3. Copie e execute o conteúdo de `database.sql`:
```bash
mysql -u root -p marketplace < database.sql
```

### Passo 2: Configurar o Banco de Dados

Edite o arquivo `config/database.php`:

```php
$servername = "localhost";
$username = "root";          // Seu usuário MySQL
$password = "";              // Sua senha MySQL
$dbname = "marketplace";
```

### Passo 3: Colocar os Arquivos no Servidor Web

**Para Apache (XAMPP/WAMP):**
```bash
# Copie a pasta marketplace para:
# Windows: C:\xampp\htdocs\marketplace
# Linux: /var/www/html/marketplace
# Mac: /Library/WebServer/Documents/marketplace
```

**Para Nginx:**
```bash
# Configure o virtual host apontando para a pasta marketplace
```

### Passo 4: Acessar o Sistema

Abra seu navegador e acesse:
```
http://localhost/marketplace
# ou
http://localhost:8000/marketplace
```

## 📁 Estrutura de Arquivos

```
marketplace/
├── config/
│   └── database.php          # Configuração do banco de dados
├── includes/
│   ├── header.php            # Header HTML compartilhado
│   ├── footer.php            # Footer HTML compartilhado
│   └── functions.php         # Funções PHP reutilizáveis
├── css/
│   └── style.css             # Estilos CSS
├── js/
│   └── script.js             # JavaScript
├── index.php                 # Página inicial
├── login.php                 # Login
├── register.php              # Registro
├── logout.php                # Logout
├── product-detail.php        # Detalhes do produto
├── cart.php                  # Carrinho de compras
├── my-products.php           # Gerenciamento de produtos (vendedor)
├── my-orders.php             # Meus pedidos (comprador)
├── admin-categories.php      # Gerenciamento de categorias (admin)
├── admin-orders.php          # Gerenciamento de pedidos (admin)
├── database.sql              # Script SQL
└── README.md                 # Este arquivo
```

## 🎯 Funcionalidades

### Para Todos os Usuários
- ✅ Visualizar todos os produtos
- ✅ Buscar produtos por nome
- ✅ Filtrar produtos por categoria
- ✅ Ver detalhes completos do produto

### Para Usuários Autenticados (Compradores)
- ✅ Adicionar produtos ao carrinho
- ✅ Gerenciar quantidade no carrinho
- ✅ Finalizar compra (criar pedido)
- ✅ Visualizar histórico de pedidos
- ✅ Ver detalhes de cada pedido

### Para Vendedores
- ✅ Cadastrar novos produtos
- ✅ Editar seus produtos
- ✅ Deletar seus produtos
- ✅ Gerenciar estoque
- ✅ Visualizar seus produtos

### Para Administradores
- ✅ Criar, editar e deletar categorias
- ✅ Visualizar todos os pedidos do sistema
- ✅ Atualizar status dos pedidos
- ✅ Ver informações completas de cada pedido

## 🔐 Segurança Implementada

### Proteção contra SQL Injection
- Uso de prepared statements via `mysqli`
- Sanitização de inputs com `htmlspecialchars()`
- Validação de tipos de dados

### Proteção contra XSS
- Escape de todo conteúdo HTML
- Validação de inputs
- Content Security Policy

### Hash de Senhas
- Uso de `password_hash()` com algoritmo BCRYPT
- Verificação com `password_verify()`
- Nunca armazenar senhas em texto plano

### Controle de Acesso
- Verificação de autenticação em páginas protegidas
- Verificação de role (admin/vendedor/comprador)
- Validação de propriedade de recursos

## 🧪 Testando o Sistema

### Criar Conta de Teste

1. Acesse `http://localhost/marketplace`
2. Clique em "Cadastro"
3. Preencha os dados:
   - Nome: "João Silva"
   - Email: "joao@email.com"
   - Senha: "senha123"
   - Tipo: "Comprador"
4. Clique em "Cadastrar"

### Fazer Login

1. Clique em "Entrar"
2. Use o email e senha criados
3. Clique em "Entrar"

### Testar como Vendedor

1. Crie outra conta com tipo "Vendedor"
2. Faça login com essa conta
3. Vá em "Meus Produtos"
4. Clique em "Criar Novo Produto"
5. Preencha os dados:
   - Nome: "Produto Teste"
   - Preço: 99.90
   - Estoque: 10
   - Categoria: Selecione uma
   - URL da Imagem: Cole uma URL válida
6. Clique em "Criar Produto"

### Testar Compra

1. Faça login como comprador
2. Na home, clique em "Ver Detalhes" de um produto
3. Clique em "Adicionar ao Carrinho"
4. Vá em "Carrinho"
5. Clique em "Finalizar Compra"
6. Veja o pedido em "Meus Pedidos"

### Testar Admin

1. Crie uma conta normal
2. No MySQL, execute:
```sql
UPDATE users SET role='admin' WHERE email='seu_email@email.com';
```
3. Faça login novamente
4. Você verá "Categorias" e "Pedidos" no menu

## 📊 Banco de Dados

### Tabelas

**users** - Usuários do sistema
```sql
id, name, email, password, role, created_at, updated_at
```

**categories** - Categorias de produtos
```sql
id, name, description, created_at, updated_at
```

**products** - Produtos
```sql
id, name, description, price, stock, category_id, seller_id, image_url, created_at, updated_at
```

**orders** - Pedidos
```sql
id, user_id, total, status, created_at, updated_at
```

**order_items** - Itens dos pedidos
```sql
id, order_id, product_id, quantity, unit_price, created_at
```

## 🔧 Configurações Importantes

### Aumentar Limite de Upload

Edite `php.ini`:
```ini
upload_max_filesize = 50M
post_max_size = 50M
```

### Timezone

Edite `php.ini`:
```ini
date.timezone = "America/Sao_Paulo"
```

### Modo de Erro

Para desenvolvimento, edite `php.ini`:
```ini
display_errors = On
error_reporting = E_ALL
```

## 🐛 Solução de Problemas

### Erro: "Erro de conexão: Access denied"
- Verifique o usuário e senha do MySQL em `config/database.php`
- Certifique-se de que o MySQL está rodando

### Erro: "Table 'marketplace.users' doesn't exist"
- Execute o script `database.sql` novamente
- Verifique se o banco de dados foi criado

### Página em branco
- Verifique o arquivo `php.ini` para erros
- Ative `display_errors` para ver mensagens de erro
- Verifique os logs do servidor web

### Carrinho não funciona
- Certifique-se de que JavaScript está habilitado
- Verifique o console do navegador (F12) para erros
- Limpe o localStorage do navegador

## 📝 Notas Importantes

- Este é um projeto educacional
- Para produção, adicione mais validações e segurança
- Considere usar HTTPS em produção
- Implemente rate limiting para login
- Adicione logs de auditoria
- Considere usar um framework PHP para projetos maiores

## 📚 Tecnologias Utilizadas

- **Frontend:** HTML5, CSS3, JavaScript ES6
- **Backend:** PHP 7.4+
- **Banco de Dados:** MySQL 5.7+
- **Servidor Web:** Apache/Nginx
- **Segurança:** BCRYPT, Prepared Statements, HTML Escape

## 👨‍💻 Autor

Desenvolvido como projeto de marketplace funcional com tecnologias básicas.

## 📄 Licença

Este projeto é fornecido como está para fins educacionais.

---

**Versão:** 1.0  
**Data:** Novembro 2025
