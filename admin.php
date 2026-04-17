<?php
session_start();
require 'php/db.php';

// Fetch all products
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all orders with item count
$orders = $pdo->query("
  SELECT o.*, COUNT(oi.id) as item_count
  FROM orders o
  LEFT JOIN order_items oi ON o.id = oi.order_id
  GROUP BY o.id
  ORDER BY o.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MiniShop — Admin</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .admin-container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
    .admin-tabs { display: flex; gap: 10px; margin-bottom: 25px; }
    .admin-tab {
      padding: 10px 24px; border-radius: 8px; border: 2px solid #ddd;
      background: white; cursor: pointer; font-weight: 600; font-size:14px;
    }
    .admin-tab.active { background: #111; color: white; border-color: #111; }
    .admin-panel { display: none; }
    .admin-panel.active { display: block; }
    table { width: 100%; border-collapse: collapse; background: white;
            border-radius: 12px; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    th { background: #111; color: white; padding: 14px 16px;
         text-align: left; font-size: 13px; }
    td { padding: 13px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #fafafa; }
    .status-badge {
      padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;
    }
    .status-pending    { background:#fff3cd; color:#856404; }
    .status-processing { background:#cce5ff; color:#004085; }
    .status-delivered  { background:#d4edda; color:#155724; }
    .add-product-form {
      background: white; border-radius: 12px; padding: 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 30px;
    }
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
  </style>
</head>
<body>

<nav>
  <div class="logo">Mini<span>Shop</span> Admin</div>
  <div class="nav-links">
    <a href="index.php">← Back to Shop</a>
  </div>
</nav>

<div class="admin-container">

  <!-- TABS -->
  <div class="admin-tabs">
    <button class="admin-tab active" onclick="switchAdminTab('products')">
      📦 Products (<?php echo count($products); ?>)
    </button>
    <button class="admin-tab" onclick="switchAdminTab('orders')">
      🧾 Orders (<?php echo count($orders); ?>)
    </button>
    <button class="admin-tab" onclick="switchAdminTab('add')">
      ➕ Add Product
    </button>
  </div>

  <!-- PRODUCTS PANEL -->
  <div class="admin-panel active" id="panel_products">
    <table>
      <thead>
        <tr>
          <th>Image</th><th>Name</th><th>Category</th>
          <th>Price</th><th>Stock</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr id="productRow_<?php echo $p['id']; ?>">
          <td>
            <img src="images/products/<?php echo $p['image']; ?>"
              style="width:55px;height:55px;object-fit:cover;border-radius:8px"
              onerror="this.src='images/products/placeholder.jpg'">
          </td>
          <td><?php echo $p['name']; ?></td>
          <td><?php echo ucfirst($p['category']); ?></td>
          <td>$<?php echo number_format($p['price'],2); ?></td>
          <td><?php echo $p['stock']; ?></td>
          <td>
            <button class="btn btn-red"
              onclick="deleteProduct(<?php echo $p['id']; ?>)">
              Delete
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- ORDERS PANEL -->
  <div class="admin-panel" id="panel_orders">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Customer</th><th>Email</th>
          <th>Items</th><th>Total</th><th>Status</th><th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?php echo $o['id']; ?></td>
          <td><?php echo $o['customer_name']; ?></td>
          <td><?php echo $o['email']; ?></td>
          <td><?php echo $o['item_count']; ?> items</td>
          <td>$<?php echo number_format($o['total'],2); ?></td>
          <td>
            <span class="status-badge status-<?php echo $o['status']; ?>">
              <?php echo ucfirst($o['status']); ?>
            </span>
          </td>
          <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- ADD PRODUCT PANEL -->
  <div class="admin-panel" id="panel_add">
    <div class="add-product-form">
      <h3 style="margin-bottom:20px">➕ Add New Product</h3>
      <div class="msg error"   id="addError"></div>
      <div class="msg success" id="addSuccess"></div>

      <div class="two-col">
        <div class="form-group">
          <label>Product Name *</label>
          <input type="text" id="newName" placeholder="e.g. Blue Hoodie">
        </div>
        <div class="form-group">
          <label>Price ($) *</label>
          <input type="number" id="newPrice" placeholder="e.g. 49.99" step="0.01">
        </div>
      </div>

      <div class="two-col">
        <div class="form-group">
          <label>Category *</label>
          <select id="newCategory">
            <option value="">-- Select --</option>
            <option value="clothing">Clothing</option>
            <option value="shoes">Shoes</option>
          </select>
        </div>
        <div class="form-group">
          <label>Stock *</label>
          <input type="number" id="newStock" placeholder="e.g. 25">
        </div>
      </div>

      <div class="form-group">
        <label>Image Filename * (e.g. hoodie_blue.jpg)</label>
        <input type="text" id="newImage" placeholder="hoodie_blue.jpg">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea id="newDesc" rows="3" placeholder="Product description..."></textarea>
      </div>

      <button class="btn btn-green" onclick="addProduct()">
        ➕ Add Product
      </button>
    </div>
  </div>

</div>

<footer>&copy; 2026 <span>MiniShop</span> — Built for learning 💚</footer>
<script src="main.js"></script>
</body>
</html>