<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
requireLogin();
require_once 'includes/header.php';

$user = getCurrentUser($conn);
$orders = getUserOrders($conn, $user['id']);

$order_detail = null;
if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']);
    $order_detail = getOrderWithItems($conn, $order_id);
    
    // Verificar se o pedido pertence ao usuário
    if (!$order_detail || $order_detail['user_id'] != $user['id']) {
        $order_detail = null;
    }
}
?>

<div class="container">
    <h2>Meus Pedidos</h2>

    <?php if ($order_detail): ?>
        <!-- Detalhes do Pedido -->
        <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3>Pedido #<?php echo $order_detail['id']; ?></h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
                <div>
                    <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($order_detail['created_at'])); ?></p>
                    <p><strong>Status:</strong> <span style="background-color: #dbeafe; color: #0c2d6b; padding: 5px 10px; border-radius: 4px;">
                        <?php 
                        $status_map = [
                            'pendente' => 'Pendente',
                            'processando' => 'Processando',
                            'concluído' => 'Concluído',
                            'cancelado' => 'Cancelado'
                        ];
                        echo $status_map[$order_detail['status']] ?? $order_detail['status'];
                        ?>
                    </span></p>
                </div>
                <div>
                    <p><strong>Total:</strong> <span style="font-size: 20px; font-weight: bold; color: #6366f1;">
                        <?php echo formatPrice($order_detail['total']); ?>
                    </span></p>
                </div>
            </div>

            <h4>Itens do Pedido</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_detail['items'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo formatPrice($item['unit_price']); ?></td>
                            <td><?php echo formatPrice($item['unit_price'] * $item['quantity']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <a href="my-orders.php" class="btn btn-secondary" style="margin-top: 20px;">Voltar</a>
        </div>
    <?php else: ?>
        <!-- Lista de Pedidos -->
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <h3>Nenhum pedido realizado</h3>
                <p>Comece a comprar para ver seus pedidos aqui</p>
                <a href="index.php" class="btn" style="display: inline-block; margin-top: 20px;">Ir para Home</a>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td><?php echo formatPrice($order['total']); ?></td>
                            <td>
                                <span style="background-color: #dbeafe; color: #0c2d6b; padding: 5px 10px; border-radius: 4px;">
                                    <?php 
                                    $status_map = [
                                        'pendente' => 'Pendente',
                                        'processando' => 'Processando',
                                        'concluído' => 'Concluído',
                                        'cancelado' => 'Cancelado'
                                    ];
                                    echo $status_map[$order['status']] ?? $order['status'];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <a href="my-orders.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary" style="display: inline-block; padding: 8px 15px;">Ver Detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
