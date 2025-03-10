<?php
session_start();
$connection = new mysqli("localhost", "root", "", "ordering_system");
$_SESSION['previous_page'] = 'order_placed.php'; 
$backLink = isset($_SESSION['previous_page']) ? $_SESSION['previous_page'] : 'order_placed.php';
$total = 0;
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    die("<div class='error'>Invalid Order! <a href='index.php'>Go back</a></div>");
}

// Fetch order details
$order_query = $connection->prepare("SELECT * FROM orders WHERE id = ?");
$order_query->bind_param("i", $order_id);
$order_query->execute();
$order_result = $order_query->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
  echo("
   <div class='no-orders-box'>
       <h2>No Orders Found</h2>
       <p>Looks like you haven't placed any orders yet.</p>
       <a href='/project/menu.php#healthy-drinks' class='button'>Go Back to Menu</a>
   </div>
   <style>
       body {
           display: flex;
           justify-content: center;
           align-items: center;
           height: 100vh;
           background-color: #f8f8f8;
           font-family: 'Poppins', sans-serif;
           margin: 0;
       }
       .no-orders-box {
           text-align: center;
           background: white;
           padding: 30px;
           border-radius: 10px;
           box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
           max-width: 400px;
       }
       .no-orders-box h2 {
           color: #013220;
           margin-bottom: 10px;
       }
       .no-orders-box p {
           color: #555;
           margin-bottom: 20px;
       }
       .button {
           display: inline-block;
           padding: 10px 20px;
           background-color: #FFC107;
           color: #013220;
           text-decoration: none;
           font-weight: bold;
           border-radius: 5px;
           transition: 0.3s;
       }
       .button:hover {
           background-color: #e6a800;
       }
   </style>
  ");
  exit;
}

// Fetch ordered items
$items_query = $connection->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items_query->bind_param("i", $order_id);
$items_query->execute();
$items_result = $items_query->get_result();
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
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            text-align: center;
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
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 10%;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: rgba(1, 50, 32, 1);
            color: white;
        }
        .status {
            font-weight: bold;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .pending { background-color: orange; }
        .completed { background-color: green; }
        .cancelled { background-color: red; }
        .preparing { background-color: #0f5298; color: white; padding: 5px; border-radius: 5px; }
        .to, .ship { background-color: darkgreen; color: white; padding: 5px; border-radius: 5px; }
        .delivered { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        .received { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        a.button {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: white;
            background: rgba(1, 50, 32, 1);
            padding: 10px 15px;
            border-radius: 5px;
        }
        a.button:hover {
            background: #45a049;
        }
        #footer {
                background-color: #013220;
                color: #fff;
                padding: 40px 20px;
                text-align: center;
                margin-top: 10%;
              }
              .footer-container {
                max-width: 1200px;
                margin: 0 auto;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
                gap: 20px;
              }
              #footer .contact-info,
              #footer .feedback,
              #footer .social-media {
                flex: 1;
                min-width: 250px;
              }
              #footer .contact-info p {
                margin: 10px 0;
                font-size: 16px;
              }
              #footer .feedback h3 {
                margin-bottom: 15px;
                font-size: 20px;
              }
              #footer .feedback form input,
              #footer .feedback form textarea {
                width: 100%;
                margin-bottom: 10px;
                padding: 10px;
                border: none;
                border-radius: 5px;
              }
              #footer .feedback form button {
                padding: 10px 20px;
                background-color: #FFC107; /* Mustard Yellow */
                color: #013220;
                border: none;
                border-radius: 5px;
                cursor: pointer;
              }
              #footer .social-media a {
                color: #FFC107;
                margin: 0 10px;
                font-size: 24px;
                text-decoration: none;
              }
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
            <a href="<?= $backLink ?>">My Purchases</a>
            <a href="/project/project-folder/php/checkout.php" class="fas fa-shopping-cart"></a>
            
        </div>
    </nav>
    <div class="container">
        <h2>ORDER SUMMARY</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <p><strong>Contact:</strong> <?php echo htmlspecialchars($order['contact']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
        <p><strong>Status:</strong> 
            <span class="status <?php echo strtolower($order['order_status']); ?>">
                <?php echo htmlspecialchars($order['order_status']); ?>
            </span>
        </p>

        <h3>Ordered Items</h3>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
            <?php while ($item = $items_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                <td>₱<?php echo number_format($item['price'], 2); ?></td>
            </tr>
            <?php $total += $item['price'] * $item['quantity']; ?>
            <?php endwhile; ?>
        </table>
        <p class="total">Total: ₱<?= number_format($total, 2) ?></p>

        <a class="button" href="<?= $backLink ?>" class="back-button">Back to Orders</a>
    </div>

    <footer id="footer">
              <div class="footer-container">
                <div class="contact-info">
                  <p><i class="fas fa-phone-alt"></i> 0961 625 3718</p>
                  <p><i class="fas fa-envelope"></i> customercare@pieexpressph.com</p>
                  <p><i class="fas fa-clock"></i> Working Hours: 9am - 5pm</p>
                </div>
                <div class="feedback">
                  <h3>Feedback</h3>
                  <form>
                    <input type="text" placeholder="Your Name" required>
                    <input type="email" placeholder="Your Email" required>
                    <textarea placeholder="Your Feedback" required></textarea>
                    <button type="submit">Submit</button>
                  </form>
                </div>
                <div class="social-media">
                  <a href="https://www.facebook.com/pieexpressph" target="_blank"><i class="fab fa-facebook-f"></i></a>
                  <a href="https://www.instagram.com/pieexpressph?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank"><i class="fab fa-instagram"></i></a>
                  <a href="www.pieexpressph.com" target="_blank"><i class="fas fa-globe"></i></a>
                </div>
              </div>
            </footer>
</body>
</html>

<?php
$order_query->close();
$items_query->close();
$connection->close();
?>
