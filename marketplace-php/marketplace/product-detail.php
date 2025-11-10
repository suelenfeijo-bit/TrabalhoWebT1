<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = getProductById($conn, $product_id);

if (!$product) {
    header("Location: index.php");
    exit();
}
?>

<div class="container">
    <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 40px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
            <!-- Imagem do Produto -->
            <div>
                <?php if ($product['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; border-radius: 8px;">
                <?php else: ?>
                    <div style="width: 100%; height: 400px; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        <span style="color: #999;">Sem imagem</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Informações do Produto -->
            <div>
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div style="margin: 20px 0;">
                    <div style="font-size: 32px; font-weight: bold; color: #6366f1;">
                        <?php echo formatPrice($product['price']); ?>
                    </div>
                </div>

                <div style="margin: 20px 0; padding: 15px; background-color: #f3f4f6; border-radius: 4px;">
                    <p><strong>Estoque:</strong> <?php echo $product['stock']; ?> unidades</p>
                    <p><strong>Categoria:</strong> <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></p>
                    <p><strong>Vendedor:</strong> <?php echo htmlspecialchars($product['seller_name'] ?? 'N/A'); ?></p>
                </div>

                <div style="margin: 20px 0;">
                    <h3>Descrição</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <?php if ($product['stock'] > 0): ?>
                    <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars(addslashes($product['name'])); ?>', <?php echo $product['price']; ?>)" class="btn" style="width: 100%; padding: 15px; font-size: 16px;">
                        Adicionar ao Carrinho
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary" style="width: 100%; padding: 15px; font-size: 16px;" disabled>
                        Fora de Estoque
                    </button>
                <?php endif; ?>

                <a href="index.php" class="btn btn-secondary" style="width: 100%; padding: 15px; font-size: 16px; margin-top: 10px;">
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
