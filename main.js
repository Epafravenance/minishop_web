// ============================================
// 🛒 ADD TO CART — sends product id to PHP
// ============================================

// productId = which product to add
// btnEl     = the button that was clicked (so we can change its text)
function addToCart(productId, btnEl) {

  // Change button text to show something is happening
  btnEl.textContent = 'Adding...';
  btnEl.disabled = true; // prevent double clicking

  // Package the product id to send to PHP
  const formData = new FormData();
  formData.append('product_id', productId);

  // Send to PHP
  fetch('php/add_to_cart.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      // Update the cart badge number in navbar
      document.getElementById('cartBadge').textContent = data.cartCount;
      btnEl.textContent = 'Added ✓';
      btnEl.style.background = '#27ae60';

      // Reset button after 2 seconds
      setTimeout(() => {
        btnEl.textContent = 'Add to Cart';
        btnEl.style.background = '';
        btnEl.disabled = false;
      }, 2000);

    } else {
      btnEl.textContent = 'Add to Cart';
      btnEl.disabled = false;
      alert(data.message);
    }
  })
  .catch(error => {
    btnEl.textContent = 'Add to Cart';
    btnEl.disabled = false;
    console.error(error);
  });
}


// ============================================
// 🔍 LIVE SEARCH — filters cards as you type
// ============================================
function filterProducts() {
  // Get what user typed, lowercase for easy comparison
  const query = document.getElementById('searchBox').value.toLowerCase();

  // Get all product cards
  const cards = document.querySelectorAll('.product-card');
  let visibleCount = 0;

  cards.forEach(card => {
    // Each card has data-name attribute we set in PHP
    const name = card.getAttribute('data-name');

    if (name.includes(query)) {
      card.style.display = 'block'; // show it
      visibleCount++;
    } else {
      card.style.display = 'none'; // hide it
    }
  });

  // Show "no results" message if nothing matches
  const noResults = document.getElementById('noResults');
  if (noResults) {
    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
  }
}

// ============================================
// 🗂️ FILTER BY CATEGORY — reloads page with ?category=
// ============================================
function filterByCategory(category) {
  window.location.href = 'shop.php?category=' + category;
}



// ============================================
// 🔢 QUANTITY CONTROL — product detail page
// ============================================

let currentQty = 1; // track current quantity

function changeQty(change) {
  // change is +1 or -1
  currentQty += change;

  // Don't let quantity go below 1
  if (currentQty < 1) currentQty = 1;

  // Update the display
  document.getElementById('qtyDisplay').textContent = currentQty;
}

// Add to cart WITH quantity (used on product detail page)
function addToCartWithQty(productId, btnEl) {
  btnEl.textContent = 'Adding...';
  btnEl.disabled = true;

  const formData = new FormData();
  formData.append('product_id', productId);
  formData.append('quantity', currentQty); // send the chosen quantity

  fetch('php/add_to_cart.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      document.getElementById('cartBadge').textContent = data.cartCount;
      btnEl.textContent = 'Added ✓';
      btnEl.style.background = '#111';
      setTimeout(() => {
        btnEl.textContent = '🛒 Add to Cart';
        btnEl.style.background = '';
        btnEl.disabled = false;
      }, 2000);
    } else {
      alert(data.message);
      btnEl.textContent = '🛒 Add to Cart';
      btnEl.disabled = false;
    }
  });
}



// ============================================
// 🛒 CART PAGE — update quantity & remove item
// ============================================

function updateCartQty(productId, change) {
  const formData = new FormData();
  formData.append('product_id', productId);
  formData.append('change', change); // +1 or -1

  fetch('php/update_cart.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {

      if (data.newQty === 0) {
        // Remove the item row from page
        document.getElementById('cartItem_' + productId).remove();
      } else {
        // Update quantity display
        document.getElementById('qty_' + productId).textContent = data.newQty;
        // Update item subtotal
        document.getElementById('subtotal_' + productId).textContent = '$' + data.subtotal;
      }

      // Update total and badge
      document.getElementById('cartTotal').textContent  = '$' + data.cartTotal;
      document.getElementById('cartBadge').textContent  = data.cartCount;

      // If cart is now empty reload to show empty state
      if (data.cartCount === 0) location.reload();
    }
  });
}

function removeFromCart(productId) {
  const formData = new FormData();
  formData.append('product_id', productId);

  fetch('php/remove_from_cart.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      document.getElementById('cartItem_' + productId).remove();
      document.getElementById('cartTotal').textContent = '$' + data.cartTotal;
      document.getElementById('cartBadge').textContent = data.cartCount;
      if (data.cartCount === 0) location.reload();
    }
  });
}



// ============================================
// ✅ PLACE ORDER — validate and send to PHP
// ============================================
function placeOrder() {
  const name    = document.getElementById('custName').value.trim();
  const email   = document.getElementById('custEmail').value.trim();
  const phone   = document.getElementById('custPhone').value.trim();
  const address = document.getElementById('custAddress').value.trim();

  // Hide old messages
  const errEl = document.getElementById('checkoutError');
  const sucEl = document.getElementById('checkoutSuccess');
  errEl.style.display = 'none';
  sucEl.style.display = 'none';

  // Validate
  if (!name)    { errEl.textContent = 'Please enter your name.';    errEl.style.display='block'; return; }
  if (!email)   { errEl.textContent = 'Please enter your email.';   errEl.style.display='block'; return; }
  if (!phone)   { errEl.textContent = 'Please enter your phone.';   errEl.style.display='block'; return; }
  if (!address) { errEl.textContent = 'Please enter your address.'; errEl.style.display='block'; return; }

  const formData = new FormData();
  formData.append('name',    name);
  formData.append('email',   email);
  formData.append('phone',   phone);
  formData.append('address', address);

  fetch('php/place_order.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      sucEl.textContent = data.message;
      sucEl.style.display = 'block';
      // Redirect to home after 3 seconds
      setTimeout(() => window.location.href = 'index.php', 3000);
    } else {
      errEl.textContent = data.message;
      errEl.style.display = 'block';
    }
  });
}