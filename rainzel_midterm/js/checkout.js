let currentStep = 1;
let selectedPayment = null;
let selectedDelivery = null;

document.getElementById('address-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (validateAddressForm()) {
        goToStep(2);
    }
});

function validateAddressForm() {
    const name = document.getElementById('name').value;
    const phone = document.getElementById('phone').value;
    const address = document.getElementById('address').value;
    
    if (!name || !phone || !address) {
        alert('Please fill in all address fields');
        return false;
    }
    return true;
}

function selectPayment(method) {
    selectedPayment = method;
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');
    
    const cardForm = document.getElementById('card-form');
    if (method === 'card') {
        cardForm.classList.remove('hidden');
    } else {
        cardForm.classList.add('hidden');
    }
}

function goToDeliveryOptions() {
    if (!selectedPayment) {
        alert('Please select a payment method');
        return;
    }
    if (selectedPayment === 'card' && !validateCardDetails()) {
        return;
    }
    goToStep(3);
}

function validateCardDetails() {
    if (selectedPayment === 'card') {
        const cardNumber = document.getElementById('card-number').value;
        const expiry = document.getElementById('expiry').value;
        const cvv = document.getElementById('cvv').value;
        
        if (!cardNumber || !expiry || !cvv) {
            alert('Please fill in all card details');
            return false;
        }
    }
    return true;
}

function selectDelivery(method) {
    selectedDelivery = method;
    document.querySelectorAll('.delivery-option').forEach(option => {
        option.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');
    
    const deliveryFee = method === 'express' ? 99 : 49;
    const subtotal = parseFloat(document.querySelector('.subtotal span:last-child').textContent.replace('₱', ''));
    
    document.getElementById('delivery-fee').textContent = `₱${deliveryFee.toFixed(2)}`;
    document.getElementById('total-amount').textContent = `₱${(subtotal + deliveryFee).toFixed(2)}`;
}

function goToStep(step) {
    currentStep = step;
    
    document.querySelectorAll('.step').forEach((el, index) => {
        el.classList.toggle('active', index + 1 === step);
    });
    
    document.querySelectorAll('.checkout-section').forEach((el, index) => {
        el.classList.toggle('active', index + 1 === step);
    });
}

function placeOrder() {
    if (!selectedDelivery) {
        alert('Please select a delivery option');
        return;
    }
    
    const orderData = {
        address: {
            name: document.getElementById('name').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value
        },
        payment: selectedPayment,
        delivery: selectedDelivery
    };
    
    fetch('../ajax/place_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'order_confirmation.php?order_id=' + data.order_id;
        } else {
            alert('Error placing order: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error placing order');
    });
}

document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedPayment = document.querySelector('input[name="payment"]:checked');
    if (!selectedPayment) {
        alert('Please select a payment method');
        return;
    }
    
    if (selectedPayment.value === 'card') {
        const cardForm = document.getElementById('card-form');
        if (!validateCardDetails()) {
            return;
        }
    }
    
    sessionStorage.setItem('paymentMethod', selectedPayment.value);
    
    goToStep(3);
});

function validateCardDetails() {
    return true;
}

function goToStep(step) {
    document.querySelectorAll('.step').forEach((el, index) => {
        el.classList.toggle('active', index + 1 <= step);
        el.classList.toggle('completed', index + 1 < step);
    });
    
    document.querySelectorAll('.checkout-section').forEach((el, index) => {
        el.classList.toggle('active', index + 1 === step);
    });
}

document.querySelectorAll('.payment-option').forEach(option => {
    const radio = option.querySelector('input[type="radio"]');
    radio.addEventListener('change', () => {
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        if (radio.checked) {
            option.classList.add('selected');
        }
    });
});

document.getElementById('delivery-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedDelivery = document.querySelector('input[name="delivery"]:checked');
    if (!selectedDelivery) {
        alert('Please select a delivery option');
        return;
    }
    
    const orderData = {
        address: {
            name: document.getElementById('name').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value
        },
        payment: sessionStorage.getItem('paymentMethod'),
        delivery: selectedDelivery.value
    };
    
    fetch('../ajax/place_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'order_confirmation.php?order_id=' + data.order_id;
        } else {
            alert('Error placing order: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error placing order. Please try again.');
    });
});

document.querySelectorAll('.delivery-option').forEach(option => {
    const radio = option.querySelector('input[type="radio"]');
    radio.addEventListener('change', () => {
        document.querySelectorAll('.delivery-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        if (radio.checked) {
            option.classList.add('selected');
            
            const deliveryFee = radio.value === 'express' ? 99 : 49;
            const subtotal = parseFloat(document.querySelector('.subtotal').textContent.replace('₱', ''));
            
            document.querySelector('.delivery-fee').textContent = '₱' + deliveryFee.toFixed(2);
            document.querySelector('.total').textContent = '₱' + (subtotal + deliveryFee).toFixed(2);
        }
    });
});
