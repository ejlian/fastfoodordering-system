let cartCount = 0;

function addToCart(itemId) {
    fetch('../ajax/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            itemId: itemId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cartCount);
            showNotification('Item added to cart!');
        } else {
            showNotification('Error adding item to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding item to cart', 'error');
    });
}

function updateCartCount(count) {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    fetch('../ajax/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            cartCount = data.count;
            updateCartCount();
        });
}); 
