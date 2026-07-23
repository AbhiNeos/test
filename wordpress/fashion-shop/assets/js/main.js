/**
 * Fashion Shop - Main JavaScript
 * Cart functionality using localStorage
 */

const CART_KEY = 'fashionShopCart';

/* === Toast Notification (replaces alert popups) === */
function showToast(message, type) {
    type = type || 'success';
    var existing = document.querySelector('.fs-toast');
    if (existing) existing.remove();

    var toast = document.createElement('div');
    toast.className = 'fs-toast fs-toast-' + type;
    toast.innerHTML = '<span>' + message + '</span><button onclick="this.parentElement.remove()">&times;</button>';
    document.body.appendChild(toast);

    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(function() { toast.remove(); }, 300);
    }, 3500);
}

function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY)) || [];
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    var cart = getCart();
    var count = cart.reduce(function(sum, item) { return sum + item.quantity; }, 0);
    var el = document.getElementById('cartCount');
    if (el) el.textContent = count;
    var mob = document.querySelector('.mobile-cart-count');
    if (mob) mob.textContent = count;
}

function addToCart(productId, productName, price) {
    var sizeBtn = document.querySelector('.size-btn.selected');
    var colorBtn = document.querySelector('.color-btn.selected');
    var qtyInput = document.getElementById('quantity');

    var size = sizeBtn ? sizeBtn.dataset.size : '';
    var color = colorBtn ? colorBtn.dataset.color : '';
    var quantity = qtyInput ? parseInt(qtyInput.value) : 1;

    if (!size && document.querySelector('.size-btn')) {
        showToast('Please select a size', 'error');
        return;
    }

    var cart = getCart();
    var idx = -1;
    for (var i = 0; i < cart.length; i++) {
        if (cart[i].productId === productId && cart[i].size === size && cart[i].color === color) {
            idx = i;
            break;
        }
    }

    if (idx > -1) {
        cart[idx].quantity += quantity;
    } else {
        cart.push({ productId: productId, name: productName, price: price, size: size, color: color, quantity: quantity });
    }

    saveCart(cart);
    showToast('Added to cart!', 'success');
}

function buyNow(productId, productName, price) {
    var sizeBtn = document.querySelector('.size-btn.selected');
    var colorBtn = document.querySelector('.color-btn.selected');
    var qtyInput = document.getElementById('quantity');

    var size = sizeBtn ? sizeBtn.dataset.size : '';
    var color = colorBtn ? colorBtn.dataset.color : '';
    var quantity = qtyInput ? parseInt(qtyInput.value) : 1;

    if (!size && document.querySelector('.size-btn')) {
        showToast('Please select a size', 'error');
        return;
    }

    var cart = getCart();
    cart.push({ productId: productId, name: productName, price: price, size: size, color: color, quantity: quantity });
    saveCart(cart);
    window.location.href = fashionShop.homeUrl + 'checkout/';
}

function removeFromCart(index) {
    var cart = getCart();
    cart.splice(index, 1);
    saveCart(cart);
    renderCart();
    showToast('Item removed', 'success');
}

function updateCartQuantity(index, delta) {
    var cart = getCart();
    cart[index].quantity += delta;
    if (cart[index].quantity < 1) cart[index].quantity = 1;
    saveCart(cart);
    renderCart();
}

function renderCart() {
    var cart = getCart();
    var emptyEl = document.getElementById('cartEmpty');
    var contentEl = document.getElementById('cartContent');
    var itemsEl = document.getElementById('cartItems');

    if (!emptyEl || !contentEl) return;

    if (cart.length === 0) {
        emptyEl.style.display = 'block';
        contentEl.style.display = 'none';
        return;
    }

    emptyEl.style.display = 'none';
    contentEl.style.display = 'grid';

    var subtotal = 0;
    var freeDelivery = 999;

    itemsEl.innerHTML = cart.map(function(item, index) {
        var itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        return '<tr><td><strong>' + item.name + '</strong>' +
            (item.size ? '<br><small>Size: ' + item.size + '</small>' : '') +
            (item.color ? '<br><small>Color: ' + item.color + '</small>' : '') +
            '</td><td>&#8377;' + item.price + '</td>' +
            '<td><div class="qty-controls"><button onclick="updateCartQuantity(' + index + ', -1)">-</button><span>' + item.quantity + '</span><button onclick="updateCartQuantity(' + index + ', 1)">+</button></div></td>' +
            '<td><strong>&#8377;' + itemTotal + '</strong></td>' +
            '<td><button onclick="removeFromCart(' + index + ')" class="btn-remove"><i class="fas fa-trash"></i></button></td></tr>';
    }).join('');

    var shipping = subtotal >= freeDelivery ? 0 : 99;
    document.getElementById('subtotal').textContent = '\u20B9' + subtotal;
    document.getElementById('shipping').textContent = shipping === 0 ? 'Free' : '\u20B9' + shipping;

    // Apply coupon discount if exists
    var coupon = JSON.parse(localStorage.getItem('fs_coupon') || 'null');
    var discountAmt = 0;
    var discountRow = document.getElementById('discountRow');
    if (coupon && coupon.discount && discountRow) {
        discountAmt = Math.round(subtotal * coupon.discount / 100);
        document.getElementById('discountAmount').textContent = '-\u20B9' + discountAmt;
        discountRow.style.display = 'flex';
    } else if (discountRow) {
        discountRow.style.display = 'none';
    }

    document.getElementById('total').textContent = '\u20B9' + (subtotal - discountAmt + shipping);
}

function renderCheckoutSummary() {
    var cart = getCart();
    var container = document.getElementById('checkoutItems');
    if (!container) return;

    if (cart.length === 0) {
        window.location.href = fashionShop.homeUrl + 'cart/';
        return;
    }

    var subtotal = 0;
    container.innerHTML = cart.map(function(item) {
        subtotal += item.price * item.quantity;
        return '<div class="checkout-item"><span>' + item.name + ' (' + (item.size || 'N/A') + ') x' + item.quantity + '</span><span>&#8377;' + (item.price * item.quantity) + '</span></div>';
    }).join('');

    var shipping = subtotal >= 999 ? 0 : 99;
    var coupon = JSON.parse(localStorage.getItem('fs_coupon') || 'null');
    var discountAmt = 0;

    document.getElementById('checkoutSubtotal').textContent = '\u20B9' + subtotal;

    var discountRow = document.getElementById('checkoutDiscountRow');
    if (coupon && coupon.discount && discountRow) {
        discountAmt = Math.round(subtotal * coupon.discount / 100);
        var lbl = document.getElementById('checkoutDiscountLabel');
        if (lbl) lbl.textContent = 'Coupon (' + coupon.code + '):';
        var dsc = document.getElementById('checkoutDiscount');
        if (dsc) dsc.textContent = '-\u20B9' + discountAmt;
        discountRow.style.display = 'flex';
    }

    document.getElementById('checkoutShipping').textContent = shipping === 0 ? 'Free' : '\u20B9' + shipping;
    document.getElementById('checkoutTotal').textContent = '\u20B9' + (subtotal - discountAmt + shipping);
}

// Init
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();

    var toggle = document.getElementById('mobileToggle');
    var menu = document.getElementById('mobileMenu');
    if (toggle && menu) {
        toggle.addEventListener('click', function() { menu.classList.toggle('active'); });
    }

    // Auto-dismiss flash messages
    setTimeout(function() {
        document.querySelectorAll('.flash-message').forEach(function(msg) {
            msg.style.opacity = '0';
            setTimeout(function() { msg.remove(); }, 300);
        });
    }, 4000);
});
