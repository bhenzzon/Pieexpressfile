<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'] ?? null;

    if ($cart_id) {
        $sql = "DELETE FROM cart WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cart_id);
        if ($stmt->execute()) {
            echo "Item removed successfully.";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
header("Location: checkout.php");
exit;
?>
