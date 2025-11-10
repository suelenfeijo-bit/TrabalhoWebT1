<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
requireAdmin($conn);
require_once 'includes/header.php';

$error = '';
$success = '';
$categories = getCategories($conn);

// Processar adição de categoria
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        
        if (!$name) {
            $error = 'Nome da categoria é obrigatório';
        } else {
            $query = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
            
            if ($conn->query($query)) {
                $success = 'Categoria criada com sucesso!';
                $categories = getCategories($conn);
            } else {
                $error = 'Erro ao criar categoria: ' . $conn->error;
            }
        }
    }
    elseif ($_POST['action'] == 'edit') {
        $category_id = intval($_POST['category_id']);
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        
        $query = "UPDATE categories SET name='$name', description='$description' WHERE id=$category_id";
        
        if ($conn->query($query)) {
            $success = 'Categoria atualizada com sucesso!';
            $categories = getCategories($conn);
        } else {
            $error = 'Erro ao atualizar categoria: ' . $conn->error;
        }
    }
    elseif ($_POST['action'] == 'delete') {
        $category_id = intval($_POST['category_id']);
        
        if ($conn->query("DELETE FROM categories WHERE id=$category_id")) {
            $success = 'Categoria deletada com sucesso!';
            $categories = getCategories($conn);
        } else {
            $error = 'Erro ao deletar categoria: ' . $conn->error;
        }
    }
}

$editing = false;
$edit_category = null;

if (isset($_GET['edit'])) {
    $category_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM categories WHERE id = $category_id");
    if ($result->num_rows > 0) {
        $editing = true;
        $edit_category = $result->fetch_assoc();
    }
}
?>

<div class="container">
    <h2>Gerenciamento de Categorias</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Formulário de Categoria -->
    <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3><?php echo $editing ? 'Editar Categoria' : 'Criar Nova Categoria'; ?></h3>
        
        <form method="POST">
            <input type="hidden" name="action" value="<?php echo $editing ? 'edit' : 'add'; ?>">
            <?php if ($editing): ?>
                <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Nome da Categoria *</label>
                <input type="text" id="name" name="name" value="<?php echo $editing ? htmlspecialchars($edit_category['name']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description"><?php echo $editing ? htmlspecialchars($edit_category['description']) : ''; ?></textarea>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">
                    <?php echo $editing ? 'Atualizar Categoria' : 'Criar Categoria'; ?>
                </button>
                <?php if ($editing): ?>
                    <a href="admin-categories.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Lista de Categorias -->
    <h3>Categorias</h3>
    
    <?php if (empty($categories)): ?>
        <div class="empty-state">
            <h3>Nenhuma categoria cadastrada</h3>
            <p>Crie sua primeira categoria acima</p>
        </div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Criada em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td><?php echo htmlspecialchars(substr($category['description'] ?? '', 0, 50)); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($category['created_at'])); ?></td>
                        <td>
                            <a href="admin-categories.php?edit=<?php echo $category['id']; ?>" class="btn btn-secondary" style="display: inline-block; padding: 8px 15px;">Editar</a>
                            <form method="POST" style="display: inline-block;" onsubmit="return confirmDelete('Tem certeza que deseja deletar esta categoria?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <button type="submit" class="btn btn-danger" style="padding: 8px 15px;">Deletar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
