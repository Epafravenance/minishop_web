<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(["status" => "error", "message" => "Invalid request."]);
  exit;
}

$product_id = intval($_POST['product_id'] ?? 0);

if ($product_id === 0) {
  echo json_encode(["status" => "error", "message" => "Invalid product."]);
  exit;
}

// Check product exists in DB
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  echo json_encode(["status" => "error", "message" => "Product not found."]);
  exit;
}

// $_SESSION['cart'] is an array like:
// [ product_id => ['product' => [...], 'quantity' => 2], ... ]

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = []; // start empty cart if none exists
}

// Read quantity — default 1 if not sent
$quantity = intval($_POST['quantity'] ?? 1);
if ($quantity < 1) $quantity = 1;

if (isset($_SESSION['cart'][$product_id])) {
  $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
  $_SESSION['cart'][$product_id] = [
    'product'  => $product,
    'quantity' => $quantity
  ];
}

// Count total items in cart
$cartCount = count($_SESSION['cart']);

echo json_encode([
  "status"    => "success",
  "message"   => "Added to cart!",
  "cartCount" => $cartCount
]);
?>