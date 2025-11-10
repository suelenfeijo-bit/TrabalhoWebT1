<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (!$email || !$password) {
        $error = 'Email e senha são obrigatórios';
    } else {
        $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: index.php");
                exit();
            } else {
                $error = 'Email ou senha incorretos';
            }
        } else {
            $error = 'Email ou senha incorretos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Marketplace</title>
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
                    <a href="register.php" class="nav-link btn-register">Cadastro</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2>Entrar</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" onsubmit="return validateLoginForm()">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Senha</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn">Entrar</button>
                </form>
                
                <p style="margin-top: 20px; text-align: center;">
                    Não tem conta? <a href="register.php" style="color: #6366f1;">Cadastre-se aqui</a>
                </p>
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
