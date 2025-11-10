<?php
session_start();
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace de Produtos</title>
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
                    <?php if (isLoggedIn()): ?>
                        <a href="cart.php" class="nav-link">Carrinho</a>
                        <a href="my-products.php" class="nav-link">Meus Produtos</a>
                        <a href="my-orders.php" class="nav-link">Meus Pedidos</a>
                        <?php 
                        $user = getCurrentUser($conn);
                        if ($user['role'] == 'admin'): 
                        ?>
                            <a href="admin-categories.php" class="nav-link">Categorias</a>
                            <a href="admin-orders.php" class="nav-link">Pedidos</a>
                        <?php endif; ?>
                        <span class="user-info">Olá, <?php echo htmlspecialchars($user['name']); ?></span>
                        <a href="logout.php" class="nav-link logout">Sair</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link btn-login">Entrar</a>
                        <a href="register.php" class="nav-link btn-register">Cadastro</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    <main class="main-content">
