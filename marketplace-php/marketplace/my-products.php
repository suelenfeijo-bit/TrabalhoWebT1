<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
requireLogin();
require_once 'includes/header.php';

$user = getCurrentUser($conn);
$products = getProductsBySeller($conn, $user['id']);
$error = '';
$success = '';

// Processar adição de produto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $category_id = isset($_POST['category_id']) && $_POST['category_id'] ? intval($_POST['category_id']) : 'NULL';
        $image_url = sanitize($_POST['image_url']);
        
        if (!$name || $price <= 0 || $stock < 0) {
            $error = 'Dados inválidos';
        } else {
            $query = "INSERT INTO products (name, description, price, stock, category_id, seller_id, image_url) 
                     VALUES ('$name', '$description', $price, $stock, $category_id, {$user['id']}, '$image_url')";
            
            if ($conn->query($query)) {
                $success = 'Produto criado com sucesso!';
                $products = getProductsBySeller($conn, $user['id']);
            } else {
                $error = 'Erro ao criar produto: ' . $conn->error;
            }
        }
    }
    elseif ($_POST['action'] == 'edit') {
        $product_id = intval($_POST['product_id']);
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $category_id = isset($_POST['category_id']) && $_POST['category_id'] ? intval($_POST['category_id']) : 'NULL';
        $image_url = sanitize($_POST['image_url']);
        
        // Verificar se o produto pertence ao vendedor
        $result = $conn->query("SELECT seller_id FROM products WHERE id = $product_id");
        $product = $result->fetch_assoc();
        
        if (!$product || $product['seller_id'] != $user['id']) {
            $error = 'Produto não encontrado ou você não tem permissão';
        } else {
            $query = "UPDATE products SET name='$name', description='$description', price=$price, stock=$stock, category_id=$category_id, image_url='$image_url' WHERE id=$product_id";
            
            if ($conn->query($query)) {
                $success = 'Produto atualizado com sucesso!';
                $products = getProductsBySeller($conn, $user['id']);
            } else {
                $error = 'Erro ao atualizar produto: ' . $conn->error;
            }
        }
    }
    elseif ($_POST['action'] == 'delete') {
        $product_id = intval($_POST['product_id']);
        
        // Verificar se o produto pertence ao vendedor
        $result = $conn->query("SELECT seller_id FROM products WHERE id = $product_id");
        $product = $result->fetch_assoc();
        
        if (!$product || $product['seller_id'] != $user['id']) {
            $error = 'Produto não encontrado ou você não tem permissão';
        } else {
            if ($conn->query("DELETE FROM products WHERE id = $product_id")) {
                $success = 'Produto deletado com sucesso!';
                $products = getProductsBySeller($conn, $user['id']);
            } else {
                $error = 'Erro ao deletar produto: ' . $conn->error;
            }
        }
    }
}

$categories = getCategories($conn);
$editing = false;
$edit_product = null;

if (isset($_GET['edit'])) {
    $product_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM products WHERE id = $product_id AND seller_id = {$user['id']}");
    if ($result->num_rows > 0) {
        $editing = true;
        $edit_product = $result->fetch_assoc();
    }
}
?>

<div class="container">
    <h2>Meus Produtos</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Formulário de Produto -->
    <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3><?php echo $editing ? 'Editar Produto' : 'Criar Novo Produto'; ?></h3>
        
        <form method="POST">
            <input type="hidden" name="action" value="<?php echo $editing ? 'edit' : 'add'; ?>">
            <?php if ($editing): ?>
                <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="name">Nome do Produto *</label>
                    <input type="text" id="name" name="name" value="<?php echo $editing ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="price">Preço (R$) *</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo $editing ? $edit_product['price'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock">Estoque *</label>
                    <input type="number" id="stock" name="stock" value="<?php echo $editing ? $edit_product['stock'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Categoria</label>
                    <select id="category_id" name="category_id">
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($editing && $edit_product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description"><?php echo $editing ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="image_url">URL da Imagem</label>
                <input type="url" id="image_url" name="image_url" value="<?php echo $editing ? htmlspecialchars($edit_product['image_url']) : ''; ?>">
                <small>Cole uma URL de imagem válida (ex: https://...)</small>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">
                    <?php echo $editing ? 'Atualizar Produto' : 'Criar Produto'; ?>
                </button>
                <?php if ($editing): ?>
                    <a href="my-products.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Lista de Produtos -->
    <h3>Seus Produtos</h3>
    
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <h3>Nenhum produto cadastrado</h3>
            <p>Crie seu primeiro produto acima</p>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
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
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <div class="product-stock">Estoque: <?php echo $product['stock']; ?></div>
                        <div class="product-category">Categoria: <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <a href="my-products.php?edit=<?php echo $product['id']; ?>" class="btn btn-secondary" style="flex: 1;">Editar</a>
                            <form method="POST" style="flex: 1;" onsubmit="return confirmDelete('Tem certeza que deseja deletar este produto?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-danger" style="width: 100%;">Deletar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
