<?php
session_start();

$product_id = intval($_POST['product_id'] ?? 0);
$change     = intval($_POST['change']     ?? 0);

if (!isset($_SESSION['cart'][$product_id])) {
  echo json_encode(["status" => "error", "message" => "Item not in cart."]);
  exit;
}

// Apply the change
$_SESSION['cart'][$product_id]['quantity'] += $change;

// If quantity hits 0 remove from cart
if ($_SESSION['cart'][$product_id]['quantity'] <= 0) {
  unset($_SESSION['cart'][$product_id]);
  $newQty = 0;
} else {
  $newQty = $_SESSION['cart'][$product_id]['quantity'];
}

// Recalculate total
$cartTotal = 0;
foreach ($_SESSION['cart'] as $item) {
  $cartTotal += $item['product']['price'] * $item['quantity'];
}

$price    = $_SESSION['cart'][$product_id]['product']['price'] ?? 0;
$subtotal = number_format($price * $newQty, 2);

echo json_encode([
  "status"    => "success",
  "newQty"    => $newQty,
  "subtotal"  => $subtotal,
  "cartTotal" => number_format($cartTotal, 2),
  "cartCount" => count($_SESSION['cart'])
]);
?>