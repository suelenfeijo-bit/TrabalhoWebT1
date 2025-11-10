<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? sanitize($_POST['role']) : 'comprador';
    
    if (!$name || !$email || !$password || !$confirm_password) {
        $error = 'Todos os campos são obrigatórios';
    } elseif ($password !== $confirm_password) {
        $error = 'As senhas não conferem';
    } elseif (strlen($password) < 6) {
        $error = 'Senha deve ter pelo menos 6 caracteres';
    } else {
        // Verificar se email já existe
        $result = $conn->query("SELECT id FROM users WHERE email = '$email'");
        
        if ($result->num_rows > 0) {
            $error = 'Este email já está registrado';
        } else {
            $hashed_password = hashPassword($password);
            
            $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
            
            if ($conn->query($query)) {
                $success = 'Cadastro realizado com sucesso! Faça login para continuar.';
            } else {
                $error = 'Erro ao registrar usuário: ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Marketplace</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>🛒 Marketplace</h1>
                </div>
                <nav class="nav">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="login.php" class="nav-link btn-login">Entrar</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2>Cadastro</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <p style="text-align: center; margin-top: 20px;">
                        <a href="login.php" class="btn">Ir para Login</a>
                    </p>
                <?php else: ?>
                    <form method="POST" onsubmit="return validateRegisterForm()">
                        <div class="form-group">
                            <label for="name">Nome Completo</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Senha</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Tipo de Conta</label>
                            <select id="role" name="role">
                                <option value="comprador">Comprador</option>
                                <option value="vendedor">Vendedor</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn">Cadastrar</button>
                    </form>
                    
                    <p style="margin-top: 20px; text-align: center;">
                        Já tem conta? <a href="login.php" style="color: #6366f1;">Faça login aqui</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Marketplace de Produtos. Todos os direitos reservados.</p>
        </div>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
