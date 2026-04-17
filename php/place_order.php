<?php
session_start();
require 'db.php';

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
  echo json_encode(["status" => "error", "message" => "Cart is empty."]);
  exit;
}

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$phone   = trim($_POST['phone']   ?? '');
$address = trim($_POST['address'] ?? '');

if (!$name || !$email || !$phone || !$address) {
  echo json_encode(["status" => "error", "message" => "All fields required."]);
  exit;
}

// Calculate total
$total = 0;
foreach ($cart as $item) {
  $total += $item['product']['price'] * $item['quantity'];
}

// Insert order into orders table
$stmt = $pdo->prepare("
  INSERT INTO orders (customer_name, email, phone, address, total)
  VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$name, $email, $phone, $address, $total]);

// Get the new order's ID
$orderId = $pdo->lastInsertId();

// Insert each cart item into order_items table
$stmt = $pdo->prepare("
  INSERT INTO order_items (order_id, product_id, quantity, price)
  VALUES (?, ?, ?, ?)
");

foreach ($cart as $productId => $item) {
  $stmt->execute([
    $orderId,
    $productId,
    $item['quantity'],
    $item['product']['price']
  ]);
}

// Clear the cart after successful order
unset($_SESSION['cart']);

echo json_encode([
  "status"  => "success",
  "message" => "🎉 Order placed! We'll contact you at $email. Redirecting..."
]);
?>