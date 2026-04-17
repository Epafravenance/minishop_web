<?php
session_start();
require 'php/db.php';

$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Get product id from URL e.g. product.php?id=3
$id = intval($_GET['id'] ?? 0);

if ($id === 0) {
  header("Location: shop.php"); // redirect if no id given
  exit;
}

// Fetch the single product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  header("Location: shop.php");
  exit;
}

// Fetch 3 related products from same category
$stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 3");
$stmt->execute([$product['category'], $id]);
$related = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MiniShop — <?php echo $product['name']; ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    .product-detail {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px;
      padding: 50px 60px;
      max-width: 1000px;
      margin: 0 auto;
    }
    .product-detail img {
      width: 100%;
      border-radius: 14px;
      object-fit: cover;
      height: 420px;
    }
    .product-info h1 {
      font-size: 28px;
      margin-bottom: 10px;
    }
    .product-info .price {
      font-size: 26px;
      color: #27ae60;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .product-info .desc {
      color: #555;
      line-height: 1.7;
      margin-bottom: 25px;
    }
    .product-info .stock {
      font-size: 13px;
      color: #888;
      margin-bottom: 20px;
    }
    .qty-control {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
    }
    .qty-control button {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: 2px solid #ddd;
      background: white;
      font-size: 18px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .qty-control button:hover {
      border-color: #111;
    }
    .qty-control span {
      font-size: 18px;
      font-weight: 700;
      min-width: 30px;
      text-align: center;
    }
    @media (max-width: 700px) {
      .product-detail { grid-template-columns: 1fr; padding: 20px; }
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

<div class="product-detail">

  <!-- LEFT: Product Image -->
  <div>
    <img
      src="images/products/<?php echo $product['image']; ?>"
      alt="<?php echo $product['name']; ?>"
      onerror="this.src='images/products/placeholder.jpg'"
      id="mainImage"
    >
  </div>

  <!-- RIGHT: Product Info -->
  <div class="product-info">
    <span class="category-tag"><?php echo ucfirst($product['category']); ?></span>
    <h1><?php echo $product['name']; ?></h1>
    <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
    <p class="desc"><?php echo $product['description']; ?></p>
    <p class="stock">📦 <?php echo $product['stock']; ?> in stock</p>

    <!-- QUANTITY CONTROL — JS updates the number -->
    <div class="qty-control">
      <button onclick="changeQty(-1)">−</button>
      <span id="qtyDisplay">1</span>
      <button onclick="changeQty(1)">+</button>
    </div>

    <button class="btn btn-green" style="width:100%; padding:14px"
      onclick="addToCartWithQty(<?php echo $product['id']; ?>, this)">
      🛒 Add to Cart
    </button>

    <button class="btn btn-outline" style="width:100%; padding:14px; margin-top:10px"
      onclick="window.location.href='shop.php'">
      ← Back to Shop
    </button>
  </div>
</div>

<!-- RELATED PRODUCTS -->
<?php if (count($related) > 0): ?>
<div class="section-title">Related Products</div>
<div class="products-grid">
  <?php foreach ($related as $r): ?>
  <div class="product-card"
    onclick="window.location.href='product.php?id=<?php echo $r['id']; ?>'">
    <img src="images/products/<?php echo $r['image']; ?>"
         alt="<?php echo $r['name']; ?>"
         onerror="this.src='images/products/placeholder.jpg'">
    <div class="card-body">
      <span class="category-tag"><?php echo ucfirst($r['category']); ?></span>
      <h3><?php echo $r['name']; ?></h3>
      <div class="price">$<?php echo number_format($r['price'], 2); ?></div>
      <button class="btn btn-dark" style="width:100%"
        onclick="event.stopPropagation(); addToCart(<?php echo $r['id']; ?>, this)">
        Add to Cart
      </button>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<footer>&copy; 2026 <span>MiniShop</span> — Built for learning 💚</footer>
<script src="main.js"></script>
</body>
</html>