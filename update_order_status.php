<?php
$connection = new mysqli("localhost", "root", "", "ordering_system");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $update_query = $connection->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $update_query->bind_param("si", $status, $order_id);
    
    if ($update_query->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $update_query->close();
}

$connection->close();
?>