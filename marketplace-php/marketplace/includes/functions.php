<?php
// Funções auxiliares

// Verificar se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Obter dados do usuário logado
function getCurrentUser($conn) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
    return $result->fetch_assoc();
}

// Redirecionar para login se não autenticado
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Redirecionar se não for admin
function requireAdmin($conn) {
    requireLogin();
    $user = getCurrentUser($conn);
    if ($user['role'] != 'admin') {
        header("Location: index.php");
        exit();
    }
}

// Hash de senha
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verificar senha
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Sanitizar entrada
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Formatar preço
function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}

// Obter todas as categorias
function getCategories($conn) {
    $result = $conn->query("SELECT * FROM categories ORDER BY name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obter todos os produtos
function getAllProducts($conn) {
    $query = "SELECT p.*, c.name as category_name, u.name as seller_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN users u ON p.seller_id = u.id 
              ORDER BY p.created_at DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obter produto por ID
function getProductById($conn, $id) {
    $id = intval($id);
    $query = "SELECT p.*, c.name as category_name, u.name as seller_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN users u ON p.seller_id = u.id 
              WHERE p.id = $id";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

// Obter produtos por vendedor
function getProductsBySeller($conn, $seller_id) {
    $seller_id = intval($seller_id);
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.seller_id = $seller_id 
              ORDER BY p.created_at DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obter pedidos do usuário
function getUserOrders($conn, $user_id) {
    $user_id = intval($user_id);
    $query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obter todos os pedidos (admin)
function getAllOrders($conn) {
    $query = "SELECT o.*, u.name as user_name, u.email as user_email 
              FROM orders o 
              LEFT JOIN users u ON o.user_id = u.id 
              ORDER BY o.created_at DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obter itens do pedido
function getOrderItems($conn, $order_id) {
    $order_id = intval($order_id);
    $query = "SELECT oi.*, p.name as product_name 
              FROM order_items oi 
              LEFT JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = $order_id";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obter pedido completo com itens
function getOrderWithItems($conn, $order_id) {
    $order_id = intval($order_id);
    $query = "SELECT o.*, u.name as user_name, u.email as user_email 
              FROM orders o 
              LEFT JOIN users u ON o.user_id = u.id 
              WHERE o.id = $order_id";
    $result = $conn->query($query);
    $order = $result->fetch_assoc();
    
    if ($order) {
        $order['items'] = getOrderItems($conn, $order_id);
    }
    
    return $order;
}

// Criar pedido
function createOrder($conn, $user_id, $items) {
    $user_id = intval($user_id);
    $total = 0;
    
    // Validar estoque e calcular total
    foreach ($items as $item) {
        $product_id = intval($item['product_id']);
        $quantity = intval($item['quantity']);
        
        $result = $conn->query("SELECT * FROM products WHERE id = $product_id");
        $product = $result->fetch_assoc();
        
        if (!$product || $product['stock'] < $quantity) {
            return false;
        }
        
        $total += $product['price'] * $quantity;
    }
    
    // Inserir pedido
    $total = number_format($total, 2, '.', '');
    $conn->query("INSERT INTO orders (user_id, total, status) VALUES ($user_id, $total, 'pendente')");
    $order_id = $conn->insert_id;
    
    // Inserir itens do pedido e atualizar estoque
    foreach ($items as $item) {
        $product_id = intval($item['product_id']);
        $quantity = intval($item['quantity']);
        
        $result = $conn->query("SELECT price FROM products WHERE id = $product_id");
        $product = $result->fetch_assoc();
        $unit_price = $product['price'];
        
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, unit_price) 
                     VALUES ($order_id, $product_id, $quantity, $unit_price)");
        
        // Atualizar estoque
        $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
    }
    
    return $order_id;
}

?>
