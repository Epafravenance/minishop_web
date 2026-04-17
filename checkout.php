<?php
session_start();
require 'php/db.php';

$cart = $_SESSION['cart'] ?? [];

// If cart is empty redirect to shop
if (empty($cart)) {
  header("Location: shop.php");
  exit;
}

$cartCount = count($cart);
$total = 0;
foreach ($cart as $item) {
  $total += $item['product']['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MiniShop — Checkout</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .checkout-grid {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 30px;
      max-width: 1000px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .order-summary {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      height: fit-content;
    }
    .order-summary h3 {
      margin-bottom: 20px;
      font-size: 17px;
    }
    .summary-item {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      padding: 8px 0;
      border-bottom: 1px solid #f0f0f0;
    }
    .summary-total {
      display: flex;
      justify-content: space-between;
      font-weight: 700;
      font-size: 18px;
      margin-top: 15px;
    }
    @media(max-width:700px) {
      .checkout-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<nav>
  <div class="logo">Mini<span>Shop</span></div>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="cart.php">
      <span class="cart-icon">🛒
        <span class="cart-badge" id="cartBadge"><?php echo $cartCount; ?></span>
      </span>
    </a>
  </div>
</nav>

<div class="checkout-grid">

  <!-- LEFT: Customer Details Form -->
  <div>
    <div class="section-title" style="margin:0 0 20px">Checkout</div>
    <div style="background:white; border-radius:12px; padding:30px;
                box-shadow:0 2px 8px rgba(0,0,0,0.06)">

      <div class="msg error"   id="checkoutError"></div>
      <div class="msg success" id="checkoutSuccess"></div>

      <div class="form-group">
        <label>Full Name *</label>
        <input type="text" id="custName" placeholder="John Doe">
      </div>
      <div class="form-group">
        <label>Email Address *</label>
        <input type="email" id="custEmail" placeholder="you@email.com">
      </div>
      <div class="form-group">
        <label>Phone Number *</label>
        <input type="tel" id="custPhone" placeholder="+255 700 000 000">
      </div>
      <div class="form-group">
        <label>Delivery Address *</label>
        <textarea id="custAddress" rows="3"
          placeholder="Street, City, Country..."></textarea>
      </div>

      <button class="btn btn-green" style="width:100%; padding:14px; font-size:16px"
        onclick="placeOrder()">
        ✅ Place Order
      </button>
    </div>
  </div>

  <!-- RIGHT: Order Summary -->
  <div class="order-summary">
    <h3>🧾 Order Summary</h3>
    <?php foreach ($cart as $item): ?>
    <div class="summary-item">
      <span><?php echo $item['product']['name']; ?> × <?php echo $item['quantity']; ?></span>
      <span>$<?php echo number_format($item['product']['price'] * $item['quantity'], 2); ?></span>
    </div>
    <?php endforeach; ?>
    <div class="summary-total">
      <span>Total</span>
      <span>$<?php echo number_format($total, 2); ?></span>
    </div>
  </div>

</div>

<footer>&copy; 2026 <span>MiniShop</span> — Built for learning 💚</footer>
<script src="main.js"></script>
</body>
</html>