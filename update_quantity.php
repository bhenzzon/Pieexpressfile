<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    if ($quantity > 0) {
        $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $cart_id);

        if ($stmt->execute()) {
            header("Location: checkout.php"); // Redirect back to cart after updating
            exit();
        } else {
            echo "Error updating quantity.";
        }
    } else {
        echo "Quantity must be at least 1.";
    }
}

?>