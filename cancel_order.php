<?php
session_start();
$connection = new mysqli("localhost", "root", "", "ordering_system");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // Ensure only pending orders can be canceled
    $check_query = $connection->prepare("SELECT order_status FROM orders WHERE id = ?");
    $check_query->bind_param("i", $order_id);
    $check_query->execute();
    $result = $check_query->get_result();
    $order = $result->fetch_assoc();

    if ($order && $order['order_status'] === 'Pending') {
        $update_query = $connection->prepare("UPDATE orders SET order_status = 'Cancelled' WHERE id = ?");
        $update_query->bind_param("i", $order_id);
        if ($update_query->execute()) {
            echo "<div class='message-box'>Order cancelled successfully.</div>";
        } else {
            echo "<div class='message-box'>Failed to cancel the order.</div>";
        }
    } else {
        echo "<div class='message-box'>You can only cancel pending orders.</div>";
    }
    
    $check_query->close();
    $update_query->close();
}
$connection->close();
?>