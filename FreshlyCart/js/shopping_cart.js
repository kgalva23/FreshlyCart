document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.openModalBtn').forEach(function(button) {
        button.addEventListener('click', function() {
            var itemId = this.getAttribute('data-item-id');
            addToCart(itemId);
        });
    });
});


document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.remove-from-cart').forEach(function(button) {
        button.addEventListener('click', function() {
            var itemId = this.getAttribute('data-item-id');
            removeFromCart(itemId);
        });
    });
    updateCartDisplay();
});

function removeFromCart(itemId) {
    var cart = getShoppingCart();
    if (cart[itemId]) {
        delete cart[itemId];
        setShoppingCart(cart);
        updateCartDisplay(); 
    }
}

function updateCartDisplay() {
    var cart = getShoppingCart();
    var totalPrice = 0;

    document.querySelectorAll('.cart-item').forEach(function(itemDiv) {
        var itemId = itemDiv.querySelector('.remove-from-cart').getAttribute('data-item-id');

        if (cart[itemId]) {
            var itemPrice = parseFloat(itemDiv.querySelector('.item-price').textContent);
            var itemQuantity = cart[itemId];
            totalPrice += itemPrice * itemQuantity;
        } else {
            itemDiv.remove();
        }
    });

    var totalPriceElement = document.getElementById('totalPrice');
    var checkoutButton = document.getElementById('checkoutButton');
    var checkoutPriceElement = document.getElementById('checkoutPrice');
    if (totalPriceElement) {
        if(totalPrice <= 0) {
            totalPriceElement.innerHTML = 'Your cart is empty';
            checkoutButton.style.display = 'none';
        } else {
            totalPriceElement.innerHTML = 'Total Price: $' + totalPrice.toFixed(2);
            checkoutPriceElement.innerHTML = 'Total Price: $' + totalPrice.toFixed(2);
            checkoutButton.style.display = '';
        }
        
    }
}



function addToCart(itemId) {
    var cart = getShoppingCart();
    if (cart[itemId]) {
        cart[itemId] += 1;
    } else {
        cart[itemId] = 1;
    }
    setShoppingCart(cart);
}

function getShoppingCart() {
    var cart = getCookie('shopping_cart');
    if (cart) {
        return JSON.parse(cart);
    } else {
        return {};
    }
}

function setShoppingCart(cart) {
    setCookie('shopping_cart', JSON.stringify(cart), 7); // Expires in 7 days
}


function setCookie(name, value, daysToLive) {
    var date = new Date();
    date.setTime(date.getTime() + (daysToLive * 24 * 60 * 60 * 1000));
    var expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
