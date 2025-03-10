<?php
session_start();
$connection = new mysqli("localhost", "root", "", "ordering_system");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch order history
$customer_contact = $_SESSION['customer_contact']; // Ensure the session has the contact

$order_query = "SELECT oh.id AS order_id, oh.customer_name, oh.contact, oh.address, oh.order_status, oh.created_at, 
                       i.product_name, i.price, i.quantity, i.subtotal
                FROM order_history oh
                JOIN order_history_items i ON oh.id = i.order_id
                WHERE oh.contact = '{$_SESSION['customer_contact']}'  -- Filter based on session contact
                ORDER BY oh.id DESC";
$order_result = $connection->query($order_query);

$orders = [];
while ($row = $order_result->fetch_assoc()) {
    $order_id = $row['order_id'];

    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'customer_name' => $row['customer_name'],
            'contact' => $row['contact'],
            'address' => $row['address'],
            'order_status' => $row['order_status'],
            'created_at' => $row['created_at'], // Store order_status for each order
            'products' => []
        ];
    }

    $orders[$order_id]['products'][] = [
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
    <title>Your Order</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #013220;
            color: white;
        }
        .completed {
            background-color: green;
            color: white;
            padding: 5px;
            border-radius: 5px;
        }
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
        a.button {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: white;
            background: rgba(1, 50, 32, 1);
            padding: 10px 15px;
            border-radius: 5px;
            margin-right: 90%;
        }
        a.button:hover {
            background: #45a049;
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
        .pending { background-color: orange; color: white; padding: 5px; border-radius: 5px; }
        .completed { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        .cancelled { background-color: red; color: white; padding: 5px; border-radius: 5px; }
        .delivered { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        .received { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        .preparing { background-color: #0f5298; color: white; padding: 5px; border-radius: 5px; }
        .to, .ship { background-color: darkgreen; color: white; padding: 5px; border-radius: 5px; }
    </style>
</head>
<body>
<nav class="navbar" id="navbar">
        <div class="logo">
            <a class="navbar-brand" href="index.html"><img src="/project/img/logo.png" alt="logo" class="img-responsive"></a> 
        </div>
        <div class="nav-links">
            <a href="/project/index.html">Home</a>
            <a href="/project/menu.php#ordernow" class="order-now">Order Now</a>
            <a href="/project/menu.php#healthy-drinks">Menu</a>
            <a href="/project/menu.php#services">Delivery</a>
            <a href="#footer">Contacts</a>
            <a href="/project/project-folder/php/order_placed.php">My Purchases</a>
            <a href="/project/project-folder/php/checkout.php" class="fas fa-shopping-cart"></a>
            
        </div>
    </nav>

    <div class="title">
        <h2>Order History</h2>
    <?php if (empty($orders)): ?>
    <div style="text-align: center; margin-top: 20px;">
        <p style="font-size: 18px; font-weight: bold; color: #013220;">You have no recorded order.</p>
        <a href="/project/project-folder/php/order_placed.php" style="display: inline-block; padding: 10px 20px; background-color: #FFC107; color: #013220; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Back to orders
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
        <th>Date</th>
        <th>Order Status</th> <!-- Added Order Status Column -->
        <th>Product Name</th>
        <th>Price</th>
        <th>Quantity</th>  <!-- Added Quantity Column -->
        <th>Subtotal</th> 
        <th>Total Price</th>
    
    </tr>
    <?php foreach ($orders as $order_id => $order): ?>
        <?php 
$total_price = 0;
foreach ($order['products'] as $product) {
    $total_price += $product['price'] * $product['quantity'];
}
?>
        <?php foreach ($order['products'] as $index => $product): ?>
            <tr>
                <?php if ($index === 0): ?>
                    <td rowspan="<?php echo count($order['products']); ?>"> <?php echo $order_id; ?> </td>
                    <td rowspan="<?php echo count($order['products']); ?>"> <?php echo htmlspecialchars($order['customer_name']); ?> </td>
                    <td rowspan="<?php echo count($order['products']); ?>"> <?php echo htmlspecialchars($order['contact']); ?> </td>
                    <td rowspan="<?php echo count($order['products']); ?>"> <?php echo htmlspecialchars($order['address']); ?> </td>   
                    <td rowspan="<?php echo count($order['products']); ?>"> <?php echo htmlspecialchars($order['created_at']); ?> </td>              
                    <td rowspan="<?php echo count($order['products']); ?>"> 
                        <span class="<?php echo strtolower($order['order_status']); ?>">
                            <?php echo htmlspecialchars($order['order_status']); ?>
                        </span>
                    </td> <!-- Show Order Status -->
                <?php endif; ?>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo $product['quantity']; ?></td> <!-- Display Quantity -->
                <td>₱<?php echo number_format($product['price'] * $product['quantity'], 2); ?></td> <!-- Display Subtotal -->
                <?php if ($index === 0): ?>
                    <td rowspan="<?php echo count($order['products']); ?>"> <strong>₱<?php echo number_format($total_price, 2); ?></strong></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <?php endif; ?>
</table>
</body>
</html>
<?php
$connection->close();
?>