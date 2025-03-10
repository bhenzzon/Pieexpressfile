<?php
include 'db_connect.php';

$sql = "
    SELECT c.id as cart_id, p.name, p.price, c.quantity, (p.price * c.quantity) as total
    FROM cart c
    JOIN products p ON c.product_id = p.id
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $grand_total = 0;
    while ($row = $result->fetch_assoc()) {
        $grand_total += $row['total'];
        echo "
        <div class='cart-item'>
            <h2>" . $row['name'] . "</h2>
            <p>Price: ₱" . $row['price'] . "</p>
            <p>Quantity: " . $row['quantity'] . "</p>
            <p>Total: ₱" . $row['total'] . "</p>
            <form method='POST' action='php/update_item.php'>
                <input type='hidden' name='cart_id' value='" . $row['cart_id'] . "'>
                <label for='quantity'>Update Quantity:</label>
                <input type='number' name='quantity' value='" . $row['quantity'] . "' min='1'>
                <button type='submit'>Update</button>
            </form>
            <form method='POST' action='php/delete_from_cart.php'>
                <input type='hidden' name='cart_id' value='" . $row['cart_id'] . "'>
                <button type='submit' style='background-color: #dc3545; color: white;'>Remove</button>
            </form>
        </div>";
    }
    echo "<h3>Grand Total: ₱" . $grand_total . "</h3>";
} else {
    echo "<p>Your cart is empty.</p>";
}

$conn->close();
?>
