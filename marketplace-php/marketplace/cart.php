<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
requireLogin();
require_once 'includes/header.php';

$user = getCurrentUser($conn);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart = json_decode($_POST['cart'], true);
    
    if (empty($cart)) {
        $error = 'Carrinho vazio';
    } else {
        $order_id = createOrder($conn, $user['id'], $cart);
        
        if ($order_id) {
            $success = 'Pedido criado com sucesso! Número do pedido: #' . $order_id;
        } else {
            $error = 'Erro ao criar pedido. Verifique o estoque dos produtos.';
        }
    }
}
?>

<div class="container">
    <h2>Carrinho de Compras</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <p style="text-align: center; margin-top: 20px;">
            <a href="my-orders.php" class="btn">Ver Meus Pedidos</a>
        </p>
    <?php else: ?>
        <div class="cart-container">
            <div class="cart-items" id="cart-items">
                <!-- Carregado via JavaScript -->
            </div>

            <div class="cart-summary">
                <h3>Resumo do Pedido</h3>
                <div class="summary-line">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal">R$ 0,00</span>
                </div>
                <div class="summary-line">
                    <span>Frete:</span>
                    <span>Grátis</span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span id="cart-total">R$ 0,00</span>
                </div>

                <form method="POST" id="checkout-form">
                    <input type="hidden" name="cart" id="cart-data">
                    <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 16px;">
                        Finalizar Compra
                    </button>
                </form>

                <a href="index.php" class="btn btn-secondary" style="width: 100%; padding: 15px; font-size: 16px; margin-top: 10px;">
                    Continuar Comprando
                </a>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            
            // Atualizar o campo hidden com os dados do carrinho
            const form = document.getElementById('checkout-form');
            form.addEventListener('submit', function(e) {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                if (cart.length === 0) {
                    e.preventDefault();
                    alert('Carrinho vazio');
                    return false;
                }
                document.getElementById('cart-data').value = JSON.stringify(cart);
            });
        });

        // Sobrescrever a função loadCart para incluir subtotal
        function loadCart() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartContainer = document.getElementById('cart-items');
            
            if (!cartContainer) return;
            
            if (cart.length === 0) {
                cartContainer.innerHTML = '<p class="empty-state">Seu carrinho está vazio</p>';
                document.getElementById('cart-subtotal').textContent = 'R$ 0,00';
                document.getElementById('cart-total').textContent = 'R$ 0,00';
                return;
            }
            
            let html = '';
            let total = 0;
            
            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                
                html += `
                    <div class="cart-item">
                        <div class="cart-item-details">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">R$ ${parseFloat(item.price).toFixed(2)}</div>
                            <div class="cart-item-quantity">
                                <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})" class="btn btn-secondary" style="width: 40px; padding: 5px;">-</button>
                                <input type="number" value="${item.quantity}" onchange="updateQuantity(${item.id}, this.value)" style="width: 60px;">
                                <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" class="btn btn-secondary" style="width: 40px; padding: 5px;">+</button>
                                <button onclick="removeFromCart(${item.id})" class="btn btn-danger" style="flex: 1;">Remover</button>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <strong>R$ ${subtotal.toFixed(2)}</strong>
                        </div>
                    </div>
                `;
            });
            
            cartContainer.innerHTML = html;
            
            document.getElementById('cart-subtotal').textContent = 'R$ ' + total.toFixed(2);
            document.getElementById('cart-total').textContent = 'R$ ' + total.toFixed(2);
        }
        </script>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
