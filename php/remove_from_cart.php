<?php
session_start();

$product_id = intval($_POST['product_id'] ?? 0);

if (isset($_SESSION['cart'][$product_id])) {
  unset($_SESSION['cart'][$product_id]);
}

// Recalculate total
$cartTotal = 0;
foreach ($_SESSION['cart'] as $item) {
  $cartTotal += $item['product']['price'] * $item['quantity'];
}

echo json_encode([
  "status"    => "success",
  "cartTotal" => number_format($cartTotal, 2),
  "cartCount" => count($_SESSION['cart'])
]);
?>