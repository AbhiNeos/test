/**
 * Fashion Shop - Main JavaScript
 * Cart functionality using localStorage
 */

const CART_KEY = 'fashionShopCart';

function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY)) || [];
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    const cart = getCart();
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    const el = document.getElementById('cartCount');
    if (el) el.textContent = count;
    const mob = document.querySelector('.mobile-cart-count');
    if (mob) mob.textContent = count;
}

function addToCart(productId, productName, price) {
    const sizeBtn = document.querySelector('.size-btn.selected');
    const colorBtn = document.querySelector('.color-btn.selected');
    const qtyInput = document.getElementById('quantity');

    const size = sizeBtn ? sizeBtn.dataset.size : '';
    const color = colorBtn ? colorBtn.dataset.color : '';
    const quantity = qtyInput ? parseInt(qtyInput.value) : 1;

    if (!size && document.querySelector('.size-btn')) {
        alert('Please select a size');
        return;
    }

    const cart = getCart();
    const idx = cart.findIndex(i => i.productId === productId && i.size === size && i.color === color);

    if (idx > -1) {
        cart[idx].quantity += quantity;
    } else {
        cart.push({ productId, name: productName, price, size, color, quantity });
    }

    saveCart(cart);
    alert('Added to cart successfully!');
}

function buyNow(productId, productName, price) {
    addToCart(productId, productName, price);
    window.location.href = fashionShop.homeUrl + 'checkout/';
}

function removeFromCart(index) {
    const cart = getCart();
    cart.splice(index, 1);
    saveCart(cart);
    renderCart();
}

function updateCartQuantity(index, delta) {
    const cart = getCart();
    cart[index].quantity += delta;
    if (cart[index].quantity < 1) cart[index].quantity = 1;
    saveCart(cart);
    renderCart();
}

function renderCart() {
    const cart = getCart();
    const emptyEl = document.getElementById('cartEmpty');
    const contentEl = document.getElementById('cartContent');
    const itemsEl = document.getElementById('cartItems');

    if (!emptyEl || !contentEl) return;

    if (cart.length === 0) {
        emptyEl.style.display = 'block';
        contentEl.style.display = 'none';
        return;
    }

    emptyEl.style.display = 'none';
    contentEl.style.display = 'grid';

    let subtotal = 0;
    const freeDelivery = 999;

    itemsEl.innerHTML = cart.map((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        return `<tr>
            <td><strong>${item.name}</strong>${item.size ? '<br><small>Size: ' + item.size + '</small>' : ''}${item.color ? '<br><small>Color: ' + item.color + '</small>' : ''}</td>
            <td>&#8377;${item.price}</td>
            <td><div class="qty-controls"><button onclick="updateCartQuantity(${index}, -1)">-</button><span>${item.quantity}</span><button onclick="updateCartQuantity(${index}, 1)">+</button></div></td>
            <td><strong>&#8377;${itemTotal}</strong></td>
            <td><button onclick="removeFromCart(${index})" class="btn-remove"><i class="fas fa-trash"></i></button></td>
        </tr>`;
    }).join('');

    const shipping = subtotal >= freeDelivery ? 0 : 99;
    document.getElementById('subtotal').textContent = '\u20B9' + subtotal;
    document.getElementById('shipping').textContent = shipping === 0 ? 'Free' : '\u20B9' + shipping;
    document.getElementById('total').textContent = '\u20B9' + (subtotal + shipping);
}

function renderCheckoutSummary() {
    const cart = getCart();
    const container = document.getElementById('checkoutItems');
    if (!container) return;

    if (cart.length === 0) {
        window.location.href = fashionShop.homeUrl + 'cart/';
        return;
    }

    let subtotal = 0;
    container.innerHTML = cart.map(item => {
        subtotal += item.price * item.quantity;
        return `<div class="checkout-item"><span>${item.name} (${item.size || 'N/A'}) x${item.quantity}</span><span>&#8377;${item.price * item.quantity}</span></div>`;
    }).join('');

    const shipping = subtotal >= 999 ? 0 : 99;
    document.getElementById('checkoutSubtotal').textContent = '\u20B9' + subtotal;
    document.getElementById('checkoutShipping').textContent = shipping === 0 ? 'Free' : '\u20B9' + shipping;
    document.getElementById('checkoutTotal').textContent = '\u20B9' + (subtotal + shipping);
}

async function placeOrder(e) {
    e.preventDefault();
    const cart = getCart();
    if (cart.length === 0) return alert('Cart is empty');

    const form = e.target;
    const subtotal = cart.reduce((s, i) => s + (i.price * i.quantity), 0);
    const shipping = subtotal >= 999 ? 0 : 99;

    const formData = new FormData();
    formData.append('action', 'fashion_shop_place_order');
    formData.append('nonce', fashionShop.nonce);
    formData.append('customerName', form.customerName.value);
    formData.append('email', form.email.value);
    formData.append('phone', form.phone.value);
    formData.append('street', form.street.value);
    formData.append('city', form.city.value);
    formData.append('state', form.state.value);
    formData.append('pincode', form.pincode.value);
    formData.append('items', JSON.stringify(cart.map(i => ({name:i.name, price:i.price, quantity:i.quantity, size:i.size||'', color:i.color||''}))));
    formData.append('totalAmount', subtotal + shipping);
    formData.append('paymentMethod', form.paymentMethod.value);

    try {
        const res = await fetch(fashionShop.ajaxurl, { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            localStorage.removeItem(CART_KEY);
            alert('Order placed successfully! Order ID: ' + data.data.orderId);
            window.location.href = fashionShop.homeUrl;
        } else {
            alert('Failed to place order. Please try again.');
        }
    } catch (err) {
        alert('Something went wrong. Please try again.');
    }
}

// Init
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();

    const toggle = document.getElementById('mobileToggle');
    const menu = document.getElementById('mobileMenu');
    if (toggle && menu) {
        toggle.addEventListener('click', function() { menu.classList.toggle('active'); });
    }

    // Auto-dismiss flash
    setTimeout(() => {
        document.querySelectorAll('.flash-message').forEach(msg => {
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 300);
        });
    }, 4000);
});
