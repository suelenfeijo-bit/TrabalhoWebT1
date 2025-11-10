<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$products = getAllProducts($conn);
$categories = getCategories($conn);
?>

<div class="container">
    <!-- Hero Section -->
    <div class="hero">
        <h2>Bem-vindo ao Marketplace</h2>
        <p>Encontre os melhores produtos com os melhores preços</p>
    </div>

    <!-- Filtros -->
    <div class="filters">
        <input type="text" id="search" placeholder="Buscar produtos...">
        <select id="category">
            <option value="">Todas as categorias</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Grid de Produtos -->
    <div class="products-grid">
        <?php if (empty($products)): ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <h3>Nenhum produto encontrado</h3>
                <p>Volte mais tarde para ver novos produtos</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-category="<?php echo $product['category_id']; ?>">
                    <div class="product-image">
                        <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center;">
                                <span style="color: #999;">Sem imagem</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?></p>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <div class="product-stock">Estoque: <?php echo $product['stock']; ?> unidades</div>
                        <div class="product-category">Categoria: <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></div>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn">Ver Detalhes</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
