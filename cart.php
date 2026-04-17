<?php
session_start();
require 'php/db.php';

$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$cart      = $_SESSION['cart'] ?? [];

// Calculate total price
$total = 0;
foreach ($cart as $item) {
  $total += $item['product']['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MiniShop — Cart</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      display: flex;
      flex-direction: column;
    }
    footer {
      margin-top: auto;
    }
    .cart-container {
      max-width: 800px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .cart-item {
      background: white;
      border-radius: 12px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .cart-item img {
      width: 90px;
      height: 90px;
      object-fit: cover;
      border-radius: 10px;
    }
    .cart-item .item-info {
      flex: 1;
    }
    .cart-item .item-info h3 {
      font-size: 16px;
      margin-bottom: 5px;
    }
    .cart-item .item-info .price {
      color: #27ae60;
      font-weight: 700;
    }
    .cart-item .qty-control {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .cart-item .qty-control button {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      border: 2px solid #ddd;
      background: white;
      cursor: pointer;
      font-size: 16px;
    }
    .cart-item .qty-control button:hover {
      border-color: #111;
    }
    .cart-summary {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      margin-top: 20px;
    }
    .cart-summary .total-line {
      display: flex;
      justify-content: space-between;
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .empty-cart {
      text-align: center;
      padding: 80px 20px;
      color: #888;
    }
    .empty-cart div { font-size: 50px; margin-bottom: 15px; }
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

<div class="cart-container">
  <div class="section-title" style="margin:0 0 20px">Your Cart</div>

  <?php if (empty($cart)): ?>
    <!-- Empty cart state -->
    <div class="empty-cart">
      <div>🛒</div>
      <p>Your cart is empty.</p>
      <button class="btn btn-green" style="margin-top:20px"
        onclick="window.location.href='shop.php'">
        Start Shopping
      </button>
    </div>

  <?php else: ?>
    <!-- Cart items -->
    <?php foreach ($cart as $productId => $item): ?>
    <div class="cart-item" id="cartItem_<?php echo $productId; ?>">
      <img
        src="images/products/<?php echo $item['product']['image']; ?>"
        alt="<?php echo $item['product']['name']; ?>"
        onerror="this.src='images/products/placeholder.jpg'"
      >
      <div class="item-info">
        <h3><?php echo $item['product']['name']; ?></h3>
        <div class="price">$<?php echo number_format($item['product']['price'], 2); ?></div>
      </div>

      <!-- Quantity control -->
      <div class="qty-control">
        <button onclick="updateCartQty(<?php echo $productId; ?>, -1)">−</button>
        <span id="qty_<?php echo $productId; ?>"><?php echo $item['quantity']; ?></span>
        <button onclick="updateCartQty(<?php echo $productId; ?>, 1)">+</button>
      </div>

      <!-- Item subtotal -->
      <div style="font-weight:700; min-width:70px; text-align:right"
        id="subtotal_<?php echo $productId; ?>">
        $<?php echo number_format($item['product']['price'] * $item['quantity'], 2); ?>
      </div>

      <!-- Remove button -->
      <button class="btn btn-red"
        onclick="removeFromCart(<?php echo $productId; ?>)">
        ✕
      </button>
    </div>
    <?php endforeach; ?>

    <!-- ORDER SUMMARY -->
    <div class="cart-summary">
      <div class="total-line">
        <span>Total</span>
        <span id="cartTotal">$<?php echo number_format($total, 2); ?></span>
      </div>
      <button class="btn btn-green" style="width:100%; padding:14px; font-size:16px"
        onclick="window.location.href='checkout.php'">
        Proceed to Checkout →
      </button>
      <button class="btn btn-outline" style="width:100%; padding:12px; margin-top:10px"
        onclick="window.location.href='shop.php'">
        ← Continue Shopping
      </button>
    </div>
  <?php endif; ?>
</div>
<script src="main.js"></script>
</body>
<footer>&copy; 2026 <span>MiniShop</span> — Built for learning 💚</footer>
</html>