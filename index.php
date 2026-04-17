<?php
// Start session so cart works across pages
session_start();
require 'php/db.php';

// Count cart items for the badge in navbar
// $_SESSION['cart'] is an array of products user added
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Fetch 4 featured products from DB to show on home page
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
$featured = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MiniShop — Home</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR -->
<nav>
  <div class="logo">Mini<span>Shop</span></div>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="cart.php">
      <!-- Cart icon with live badge -->
      <span class="cart-icon">
        🛒
        <span class="cart-badge" id="cartBadge">
          <?php echo $cartCount; ?>
        </span>
      </span>
    </a>
  </div>
</nav>

<!-- HERO SECTION -->
<div class="hero">
  <h1>Fresh Styles.<br>Great Prices.</h1>
  <p>Shop the latest clothing and sneakers — delivered to your door.</p>
  <div class="hero-buttons">
    <button class="btn btn-green"
      onclick="window.location.href='shop.php'">
      Shop Now
    </button>
    <button class="btn btn-outline" style="color:white; border-color:white"
      onclick="window.location.href='shop.php?category=shoes'">
      View Shoes
    </button>
  </div>
</div>

<!-- FEATURED PRODUCTS -->
<div class="section-title">Featured Products</div>
<div class="section-subtitle">Handpicked just for you</div>

<div class="products-grid">
  <?php foreach ($featured as $product): ?>
  <div class="product-card"
    onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
    <img
      src="images/products/<?php echo $product['image']; ?>"
      alt="<?php echo $product['name']; ?>"
      onerror="this.src='images/products/placeholder.jpg'"
    >
    <div class="card-body">
      <span class="category-tag"><?php echo ucfirst($product['category']); ?></span>
      <h3><?php echo $product['name']; ?></h3>
      <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
      <button class="btn btn-dark" style="width:100%"
        onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, this)">
        Add to Cart
      </button>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- CATEGORIES SECTION -->
<div class="section-title" style="margin-top:40px">Shop by Category</div>
<div style="display:flex; gap:20px; padding:20px 40px;">

  <div onclick="window.location.href='shop.php?category=clothing'"
    style="flex:1; background:linear-gradient(135deg,#111,#444);
           color:white; border-radius:12px; padding:35px;
           cursor:pointer; text-align:center; font-size:18px; font-weight:700;">
    👕 Clothing
  </div>

  <div onclick="window.location.href='shop.php?category=shoes'"
    style="flex:1; background:linear-gradient(135deg,#27ae60,#1e8449);
           color:white; border-radius:12px; padding:35px;
           cursor:pointer; text-align:center; font-size:18px; font-weight:700;">
    👟 Shoes
  </div>

</div>

<!-- FOOTER -->
<footer>
  &copy; 2026 <span>MiniShop</span> — Built for learning 💚
</footer>

<script src="main.js"></script>
</body>
</html>