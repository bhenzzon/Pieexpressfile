<?php
session_start();
$connection = new mysqli("localhost", "root", "", "ordering_system");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Handle order status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    if ($order_status === "Completed") {
        // Insert into order_history
        $move_query = "INSERT INTO order_history (id, customer_name, contact, address, order_status)
                       SELECT id, customer_name, contact, address, order_status FROM orders WHERE id = ?";
        $stmt = $connection->prepare($move_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();
    
        // Insert order items into order_history_items
        $move_items_query = "INSERT INTO order_history_items (order_id, product_name, price, quantity, subtotal)
                             SELECT order_id, product_name, price, quantity, (price * quantity) FROM order_items WHERE order_id = ?";
        $stmt = $connection->prepare($move_items_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();
    
        // Delete the order from the main orders table
        $delete_query = "DELETE FROM orders WHERE id = ?";
        $stmt = $connection->prepare($delete_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();
    
        // Delete order items
        $delete_items_query = "DELETE FROM order_items WHERE order_id = ?";
        $stmt = $connection->prepare($delete_items_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();
    
    } else {
        // Just update the status if not completed
        $update_query = "UPDATE orders SET order_status = ? WHERE id = ?";
        $stmt = $connection->prepare($update_query);
        $stmt->bind_param("si", $order_status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
    exit;
}

// Handle payment status update separately
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'], $_POST['payment_status'])) {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];

    $update_payment_query = "UPDATE orders SET payment_status = ? WHERE id = ?";
    $stmt = $connection->prepare($update_payment_query);
    $stmt->bind_param("si", $payment_status, $order_id);
    $stmt->execute();
    $stmt->close();
    exit;
}

// Fetch orders
$order_query = "SELECT o.id AS order_id, o.customer_name, o.contact, o.address, o.order_status, o.payment_status,
                       i.product_name, i.price, i.quantity, (i.price * i.quantity) AS subtotal
                FROM orders o
                JOIN order_items i ON o.id = i.order_id
                ORDER BY o.id DESC";
$order_result = $connection->query($order_query);

$orders = [];
while ($row = $order_result->fetch_assoc()) {
    $orders[$row['order_id']]['customer_name'] = $row['customer_name'];
    $orders[$row['order_id']]['contact'] = $row['contact'];
    $orders[$row['order_id']]['address'] = $row['address'];
    $orders[$row['order_id']]['order_status'] = $row['order_status'];
    $orders[$row['order_id']]['payment_status'] = $row['payment_status'];
    $orders[$row['order_id']]['products'][] = [
        'name' => $row['product_name'],
        'price' => $row['price'],
        'quantity' => $row['quantity'],
        'subtotal' => $row['subtotal']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link rel="icon" type="image/x-icon" href="/project/img/logo.png">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      text-align: center;
      background-color: #f8f8f8;
    }
        table { width: 100%; border-collapse: collapse; margin-top: 3%; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #013220; color: white; }
        .pending { background-color: orange; color: white; padding: 5px; border-radius: 5px; }
        .unpaid { background-color: orange; color: white; padding: 5px; border-radius: 5px; }
        .completed { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        .cancelled { background-color: red; color: white; padding: 5px; border-radius: 5px; }
        .delivered { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        .received { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        .paid    { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        .preparing { background-color: #0f5298; color: white; padding: 5px; border-radius: 5px; }
        .to, .ship { background-color: darkgreen; color: white; padding: 5px; border-radius: 5px; }
        .btn { padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; color: white; margin: 2px; }
        .accept-btn { background-color: green; }
        .pay-btn { background-color: green; }
        .reject-btn { background-color: red; }
        .delivered-btn { background-color: Green;}
        .prepare-btn { background-color: #0f5298; }
        .ship-btn { background-color: darkgreen; }
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: rgba(1, 50, 32, 1);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
            z-index: 1000;
            height: 80px;
            padding: 10px 20px;
        }
        .navbar.scrolled {
            background-color: rgba(1, 50, 32, 0.8);
        }
        .logo {
            display: flex;
            align-items: center;
        }
        .logo img {
            width: 90px;
            height: auto;
            max-height: 100%;
        }
        .logo span {
            color: white;
            font-size: 24px;
            font-weight: 600;
        }
    
    .nav-links {
      display: flex;
      gap: 20px;
      margin-right: 50px;
    }
    .nav-links a {
      color: white;
      text-decoration: none;
      padding: 8px 12px;
      font-weight: 600;
      border-radius: 5px;
    }
    .order-now {
      background-color: #FFC107;
      color: #013220;
      padding: 10px 15px;
      border-radius: 5px;
    }
    .title {
        margin-top: 9%;
        color: rgba(1, 50, 32, 1);
    }
    .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 100%;
            margin: auto;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 10%;
            text-align: left;
        }

    </style>
    <script>
function updateOrderStatus(orderId, status) {
    fetch('orders.php', {
        method: 'POST',
        body: new URLSearchParams({ order_id: orderId, order_status: status }),
    }).then(() => location.reload());
}

function updatePaymentStatus(orderId, status) {
    fetch('orders.php', {
        method: 'POST',
        body: new URLSearchParams({ order_id: orderId, payment_status: status }),
    }).then(() => location.reload());
}
</script>
</head>
<body>
     <nav class="navbar" id="navbar">
        <div class="logo">
            <a class="navbar-brand" href="/project/project-folder/index.php"><img src="/project/img/logo.png" alt="logo" class="img-responsive"></a> 
        </div>
        <div class="nav-links">
            <a href="/project/project-folder/index.php" class="order-now">Home</a>
            <a href="/project/project-folder/php/load_products.php">Store</a>
            <a href="/project/project-folder/home.html">Add Item</a>
            <a href="/project/project-folder/php/orders.php">Orders</a>    
            <a href="/project/project-folder/php/order_history.php">History</a>        
        </div>
    </nav>
    <div class="container">
    <h2>Order Management</h2>
    
    <?php if (empty($orders)): ?>
    <div style="text-align: center; margin-top: 20px;">
        <p style="font-size: 18px; font-weight: bold; color: #013220;">No orders for now.</p>
        <a href="order_history.php" style="display: inline-block; padding: 10px 20px; background-color: #FFC107; color: #013220; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Check Order History
        </a>
    </div>
    </div>
<?php else: ?>
    <table>
    <tr>
        <th>Order ID</th>
        <th>Customer Name</th>
        <th>Contact</th>
        <th>Address</th>
        <th>Product Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
        <th>Total Price</th>
        <th>Status</th>
        <th>Payment Status</th>
        <th>Action</th>
    </tr>
    <?php foreach ($orders as $order_id => $order): ?>
        <?php 
        $total_price = array_sum(array_map(function($product) {
            return $product['price'] * $product['quantity'];
        }, $order['products']));

        // Disable "Completed" button unless the status is "Delivered"
        $disableCompleted = !in_array($order['order_status'], ['Delivered', 'Cancelled']) ? 'disabled' : '';

        // Disable "Paid" button if already paid
        $disablePaid = ($order['payment_status'] === 'Paid') ? 'disabled' : '';
        ?>
        <?php foreach ($order['products'] as $index => $product): ?>
            <tr>
                <?php if ($index === 0): ?>
                    <td rowspan="<?php echo count($order['products']); ?>"> <?php echo $order_id; ?> </td>
                    <td rowspan="<?php echo count($order['products']); ?>"> <?php echo htmlspecialchars($order['customer_name']); ?> </td>
                    <td rowspan="<?php echo count($order['products']); ?>"> <?php echo htmlspecialchars($order['contact']); ?> </td>
                    <td rowspan="<?php echo count($order['products']); ?>"> <?php echo htmlspecialchars($order['address']); ?> </td>
                <?php endif; ?>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo $product['quantity']; ?></td>
                <td>₱<?php echo number_format($product['subtotal'], 2); ?></td>
                <?php if ($index === 0): ?>
                    <td rowspan="<?php echo count($order['products']); ?>"> <strong>₱<?php echo number_format($total_price, 2); ?></strong> </td>
                    <td rowspan="<?php echo count($order['products']); ?>">
                        <span class="<?php echo strtolower($order['order_status']); ?>"> <?php echo ucfirst($order['order_status']); ?> </span>
                    </td>
                    <td rowspan="<?php echo count($order['products']); ?>">
                        <span class="<?php echo strtolower($order['payment_status']); ?>"> <?php echo ucfirst($order['payment_status']); ?> </span>
                    </td>
                    <td rowspan="<?php echo count($order['products']); ?>">
                        <button class="btn prepare-btn" onclick="updateOrderStatus(<?php echo $order_id; ?>, 'Preparing')">Preparing</button>
                        <button class="btn ship-btn" onclick="updateOrderStatus(<?php echo $order_id; ?>, 'To Ship')">To Ship</button>
                        <button class="btn delivered-btn" onclick="updateOrderStatus(<?php echo $order_id; ?>, 'Delivered')">Delivered</button>
                        <button class="btn accept-btn" onclick="updateOrderStatus(<?php echo $order_id; ?>, 'Completed')" <?php echo $disableCompleted; ?>>Completed</button>
                        <button class="btn reject-btn" onclick="updateOrderStatus(<?php echo $order_id; ?>, 'Cancelled')">Reject</button>
                        <button class="btn pay-btn" onclick="updatePaymentStatus(<?php echo $order_id; ?>, 'Paid')" <?php echo $disablePaid; ?>>Paid</button>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>
<?php endif; ?>
    
</html>
<?php
$connection->close();
?>