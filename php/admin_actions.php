<?php
require 'db.php';

$action = $_POST['action'] ?? '';

// ---- ADD PRODUCT ----
if ($action === 'add') {
  $name     = trim($_POST['name']     ?? '');
  $price    = floatval($_POST['price']    ?? 0);
  $category = trim($_POST['category'] ?? '');
  $stock    = intval($_POST['stock']   ?? 0);
  $image    = trim($_POST['image']    ?? '');
  $desc     = trim($_POST['desc']     ?? '');

  if (!$name || !$price || !$category || !$stock || !$image) {
    echo json_encode(["status" => "error", "message" => "All fields required."]);
    exit;
  }

  $stmt = $pdo->prepare("
    INSERT INTO products (name, price, category, stock, image, description)
    VALUES (?, ?, ?, ?, ?, ?)
  ");
  $stmt->execute([$name, $price, $category, $stock, $image, $desc]);

  echo json_encode(["status" => "success", "message" => "Product added."]);
}

// ---- DELETE PRODUCT ----
elseif ($action === 'delete') {
  $id = intval($_POST['product_id'] ?? 0);

  $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
  $stmt->execute([$id]);

  echo json_encode(["status" => "success"]);
}

else {
  echo json_encode(["status" => "error", "message" => "Unknown action."]);
}
?>