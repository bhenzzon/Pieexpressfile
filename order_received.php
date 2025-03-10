<?php
session_start();
$connection = new mysqli("localhost", "root", "", "ordering_system");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["order_id"])) {
    $order_id = intval($_POST["order_id"]);

    // Check if order exists
    $check_query = $connection->prepare("SELECT order_status FROM orders WHERE id = ?");
    $check_query->bind_param("i", $order_id);
    $check_query->execute();
    $result = $check_query->get_result();
    
    if ($result->num_rows === 0) {
        echo "Order not found.";
        exit;
    }
    
    $order = $result->fetch_assoc();
    if ($order["order_status"] !== "To Ship") {
        echo "Order is not in 'To Ship' status.";
        exit;
    }

    // Update the order status
    $update_query = $connection->prepare("UPDATE orders SET order_status = 'Received' WHERE id = ?");
    $update_query->bind_param("i", $order_id);

    if ($update_query->execute()) {
        echo "Order marked as received";
    } else {
        echo "Failed to update order status: " . $connection->error;
    }

    $update_query->close();
    $check_query->close();
}

$connection->close();
?>