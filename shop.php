<?php
session_start();
require 'php/db.php';

$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Check if a category filter was passed in the URL
// e.g. shop.php?category=shoes
$category = $_GET['category'] ?? 'all';

// Build query based on category
if ($category === 'all') {
  $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
} else {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
  $stmt->execute([$category]);
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MiniShop — Shop</title>
  <link rel="stylesheet" href="style.css">
  <style>
      body {
      display: flex;
      flex-direction: column;
    }
    footer {
      margin-top: auto;
    }
    .shop-header {
      padding: 30px 40px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
    }
    .filter-bar {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .filter-btn {
      padding: 8px 18px;
      border-radius: 20px;
      border: 2px solid #ddd;
      background: white;
      cursor: pointer;
      font-size: 13px;
      font-weight: 600;
      transition: all 0.2s;
    }
    .filter-btn.active,
    .filter-btn:hover {
      background: #111;
      color: white;
      border-color: #111;
    }
    .search-box {
      padding: 10px 16px;
      border: 2px solid #ddd;
      border-radius: 25px;
      font-size: 14px;
      width: 250px;
      transition: border 0.2s;
    }
    .search-box:focus {
      outline: none;
      border-color: #27ae60;
    }
    .no-results {
      text-align: center;
      padding: 60px;
      color: #888;
      font-size: 16px;
      display: none;
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

<div class="shop-header">
  <div>
    <div class="section-title" style="margin:0">All Products</div>
    <div style="color:#888; font-size:13px; margin-top:4px">
      <?php echo count($products); ?> products found
    </div>
  </div>

  <!-- SEARCH BOX — JS filters cards live as you type -->
  <input
    type="text"
    class="search-box"
    id="searchBox"
    placeholder="🔍 Search products..."
    oninput="filterProducts()"
  >

  <!-- FILTER BUTTONS -->
  <div class="filter-bar">
    <button class="filter-btn <?php echo $category==='all'      ? 'active':'' ?>"
      onclick="filterByCategory('all')">All</button>
    <button class="filter-btn <?php echo $category==='clothing' ? 'active':'' ?>"
      onclick="filterByCategory('clothing')">👕 Clothing</button>
    <button class="filter-btn <?php echo $category==='shoes'    ? 'active':'' ?>"
      onclick="filterByCategory('shoes')">👟 Shoes</button>
  </div>
</div>

<!-- PRODUCTS GRID -->
<div class="products-grid" id="productsGrid">
  <?php foreach ($products as $product): ?>
  <div class="product-card"
    data-name="<?php echo strtolower($product['name']); ?>"
    data-category="<?php echo $product['category']; ?>"
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

<div class="no-results" id="noResults">
  😕 No products found. Try a different search.
</div>

<footer>
  &copy; 2026 <span>MiniShop</span> — Built for learning 💚
</footer>

<script src="main.js"></script>
</body>
</html>