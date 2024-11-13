function updateQuantity(itemId, change) {
    const quantityInput = document.getElementById(`quantity-${itemId}`);
    let newQuantity = parseInt(quantityInput.value) + change;
    
    // Ensure quantity doesn't go below 1
    if (newQuantity < 1) {
        newQuantity = 1;
        return;
    }
    
    fetch('../ajax/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            itemId: itemId,
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update quantity display
            quantityInput.value = newQuantity;
            
            // Update item total
            const price = parseFloat(document.getElementById(`price-${itemId}`).dataset.price);
            document.getElementById(`total-${itemId}`).textContent = '₱' + (price * newQuantity).toFixed(2);
            
            // Update cart summary
            updateCartSummary(data.subtotal);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating quantity');
    });
}

function removeItem(itemId) {
    if (confirm('Are you sure you want to remove this item?')) {
        fetch('../ajax/remove_cart_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ itemId: itemId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the item from DOM
                const itemElement = document.getElementById(`cart-item-${itemId}`);
                itemElement.remove();
                
                // Update cart summary
                updateCartSummary(data.subtotal);
                updateCartCount(data.cartCount);
                
                // Reload if cart is empty
                if (data.cartCount === 0) {
                    location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing item');
        });
    }
}

function updateCartSummary(subtotal) {
    const deliveryFee = 49;
    document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
    document.getElementById('total-order').textContent = '₱' + (subtotal + deliveryFee).toFixed(2);
}

function updateCartCount(count) {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
} 
