<?php
include 'db_connect.php';

$cart_id = $_POST['cart_id'];

// Delete the item from the cart
$sql = "DELETE FROM cart WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);

if ($stmt->execute()) {
    echo "Item removed from cart.";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
