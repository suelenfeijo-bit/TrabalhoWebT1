// Funções JavaScript para o Marketplace

// Buscar e filtrar produtos
function searchProducts() {
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    
    if (searchInput && categorySelect) {
        const searchTerm = searchInput.value.toLowerCase();
        const category = categorySelect.value;
        
        const products = document.querySelectorAll('.product-card');
        
        products.forEach(product => {
            const name = product.querySelector('.product-name').textContent.toLowerCase();
            const productCategory = product.dataset.category || '';
            
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = category === '' || productCategory === category;
            
            if (matchesSearch && matchesCategory) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });
    }
}

// Adicionar ao carrinho
function addToCart(productId, productName, productPrice) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: productPrice,
            quantity: 1
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    alert('Produto adicionado ao carrinho!');
}

// Remover do carrinho
function removeFromCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    location.reload();
}

// Atualizar quantidade no carrinho
function updateQuantity(productId, quantity) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const item = cart.find(item => item.id === productId);
    
    if (item) {
        item.quantity = parseInt(quantity);
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            localStorage.setItem('cart', JSON.stringify(cart));
            location.reload();
        }
    }
}

// Carregar carrinho
function loadCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartContainer = document.getElementById('cart-items');
    
    if (!cartContainer) return;
    
    if (cart.length === 0) {
        cartContainer.innerHTML = '<p class="empty-state">Seu carrinho está vazio</p>';
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
    
    const totalElement = document.getElementById('cart-total');
    if (totalElement) {
        totalElement.textContent = 'R$ ' + total.toFixed(2);
    }
}

// Validar formulário de login
function validateLoginForm() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        alert('Por favor, preencha todos os campos');
        return false;
    }
    
    if (!email.includes('@')) {
        alert('Email inválido');
        return false;
    }
    
    return true;
}

// Validar formulário de registro
function validateRegisterForm() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (!name || !email || !password || !confirmPassword) {
        alert('Por favor, preencha todos os campos');
        return false;
    }
    
    if (!email.includes('@')) {
        alert('Email inválido');
        return false;
    }
    
    if (password.length < 6) {
        alert('Senha deve ter pelo menos 6 caracteres');
        return false;
    }
    
    if (password !== confirmPassword) {
        alert('Senhas não conferem');
        return false;
    }
    
    return true;
}

// Validar formulário de produto
function validateProductForm() {
    const name = document.getElementById('name').value;
    const price = document.getElementById('price').value;
    const stock = document.getElementById('stock').value;
    
    if (!name || !price || !stock) {
        alert('Por favor, preencha todos os campos obrigatórios');
        return false;
    }
    
    if (parseFloat(price) <= 0) {
        alert('Preço deve ser maior que zero');
        return false;
    }
    
    if (parseInt(stock) < 0) {
        alert('Estoque não pode ser negativo');
        return false;
    }
    
    return true;
}

// Validar formulário de categoria
function validateCategoryForm() {
    const name = document.getElementById('name').value;
    
    if (!name) {
        alert('Por favor, preencha o nome da categoria');
        return false;
    }
    
    return true;
}

// Confirmar exclusão
function confirmDelete(message) {
    return confirm(message || 'Tem certeza que deseja deletar?');
}

// Event listeners ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    // Carregar carrinho se existir
    loadCart();
    
    // Adicionar listeners de busca
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    
    if (searchInput) {
        searchInput.addEventListener('input', searchProducts);
    }
    
    if (categorySelect) {
        categorySelect.addEventListener('change', searchProducts);
    }
});
