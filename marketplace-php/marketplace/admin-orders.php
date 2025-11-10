<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
requireAdmin($conn);
require_once 'includes/header.php';

$orders = getAllOrders($conn);

$order_detail = null;
if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']);
    $order_detail = getOrderWithItems($conn, $order_id);
}

// Atualizar status do pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $order_id = intval($_POST['order_id']);
    $status = sanitize($_POST['status']);
    
    $valid_statuses = ['pendente', 'processando', 'concluído', 'cancelado'];
    if (in_array($status, $valid_statuses)) {
        $conn->query("UPDATE orders SET status='$status' WHERE id=$order_id");
        header("Location: admin-orders.php?id=$order_id");
        exit();
    }
}
?>

<div class="container">
    <h2>Gerenciamento de Pedidos</h2>

    <?php if ($order_detail): ?>
        <!-- Detalhes do Pedido -->
        <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3>Pedido #<?php echo $order_detail['id']; ?></h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
                <div>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($order_detail['user_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order_detail['user_email']); ?></p>
                    <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($order_detail['created_at'])); ?></p>
                </div>
                <div>
                    <p><strong>Total:</strong> <span style="font-size: 20px; font-weight: bold; color: #6366f1;">
                        <?php echo formatPrice($order_detail['total']); ?>
                    </span></p>
                    
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" value="<?php echo $order_detail['id']; ?>">
                        <div style="display: flex; gap: 10px;">
                            <select name="status" style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="pendente" <?php echo $order_detail['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                <option value="processando" <?php echo $order_detail['status'] == 'processando' ? 'selected' : ''; ?>>Processando</option>
                                <option value="concluído" <?php echo $order_detail['status'] == 'concluído' ? 'selected' : ''; ?>>Concluído</option>
                                <option value="cancelado" <?php echo $order_detail['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                            <button type="submit" class="btn" style="padding: 8px 20px;">Atualizar</button>
                        </div>
                    </form>
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

            <a href="admin-orders.php" class="btn btn-secondary" style="margin-top: 20px;">Voltar</a>
        </div>
    <?php else: ?>
        <!-- Lista de Pedidos -->
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <h3>Nenhum pedido realizado</h3>
                <p>Não há pedidos para exibir</p>
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Email</th>
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
                            <td><?php echo htmlspecialchars($order['user_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['user_email'] ?? 'N/A'); ?></td>
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
                                <a href="admin-orders.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary" style="display: inline-block; padding: 8px 15px;">Ver Detalhes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
