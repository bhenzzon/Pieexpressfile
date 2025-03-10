<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart - Pie Express</title>
  <link rel="icon" type="image/x-icon" href="/project/img/logo.png">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1{
            text-align:center;
            font-family: verdana;
        }
        .container {
            width: 80%;
            max-width: 800px;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: rgba(1, 50, 32, 1);
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            font-weight: bold;
            padding: 10px 0;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
        }
        button {
            padding: 10px 20px;
            font-size: 1rem;
            color: white;
            background-color: rgba(1, 50, 32, 1);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        button:hover {
            background-color: #FFC107;
        }
        body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      text-align: center;
      background-color: #f8f8f8;
    }
    /* Nav Bar Styles */
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
    .carousel {
            position: relative;
            width: 100%;
            height: 650px; /* Adjust this value to reduce height */
            margin: 50px auto 0;
            overflow: hidden;
        }

        .carousel-container {
            display: flex;
            transition: transform 3.0s ease-in-out;
            height: 100%;
        }

        .slide {
            min-width: 100%;
            height: 100%;
            display: none;
            position: relative;
        }

        .slide::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4); /* Adjust for more or less darkness */
            z-index: 1; /* Keeps it below the text */
        }
        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures the image fills the container while maintaining aspect ratio */
        }

        .carousel-text {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 30px;
            font-weight: bold;
            text-align: left;
            width: 40%;
            z-index: 2; /* Ensures text is above the dark overlay */
        }


        .carousel-text p {
            font-size: 18px;
            font-weight: 300;
        }
        .carousel-text.left {
            left: 5%;
        }
        .carousel-text.right {
            right: 5%;
            text-align: right;
        }
        .prev, .next-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        .prev {
            left: 10px;
        }
        .next-btn {
            right: 10px;
        }
        .order-now-overlay {
            display: inline-block;
            background-color: #FFC107;
            color: #013220;
            padding: 12px 20px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
            position: relative; /* Keeps it aligned within the text block */
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
            <a href="/project/project-folder/php/order_placed.php">My Purchases</a>
            <a href="/project/project-folder/php/checkout.php" class="fas fa-shopping-cart"></a>
            
        </div>
    </nav>
  <div class="carousel">
        <button class="prev" onclick="prevSlide()">&#10094;</button>
        <div class="carousel-container" id="carousel">
            <div class="slide active">
                <img src="/project/img/bg1.jpg">
                <div class="carousel-text left">
                    <h1>PIE EXPRESS</h1>
                    <p>Making a healthier Philippines,<br> One kuchay Pie at a time!<br>TO OUR HEALTH REVOLUTION!</p>
                    <a href="/project/index.html" class="order-now-overlay">See More...</a> <!-- Added Button -->
                </div>
            </div>
            <div class="slide">
                <img src="/project/img/bg2.JPG">
                <div class="carousel-text right">
                    <h1>PIE EXPRESS</h1>
                    <p>Making a healthier Philippines.</p>
                    <a href="/project/index.html" class="order-now-overlay">See More...</a> <!-- Added Button -->
                </div>
            </div>
            <div class="slide">
                <img src="/project/img/bg4.jpg">
                <div class="carousel-text left">
                    <h1>PIE EXPRESS</h1>
                    <p>Bringing sustainable, healthy, and good quality products to all.</p>
                    <a href="/project/index.html" class="order-now-overlay">See More...</a> <!-- Added Button -->
                </div>
            </div>
            <div class="slide">
                <img src="/project/img/bg3.jpg">
                <div class="carousel-text right">
                    <h1>PIE EXPRESS</h1>
                    <p>Customers deserve the healthy and best quality products and service.<br>Compassion for PIE EXPRESS and to its employees.</p>
                    <a href="/project/index.html" class="order-now-overlay">See More...</a> <!-- Added Button -->
                </div>
            </div>
        </div>
        <button class="next-btn" onclick="nextSlide()">&#10095;</button>
            </div>
            <?php
include 'db_connect.php'; // Ensure database connection is included

// Fetch cart data with product details
$sql = "SELECT cart.id as cart_id, products.name, products.price, cart.quantity 
        FROM cart 
        JOIN products ON cart.product_id = products.id";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching cart data: " . $conn->error); // Debugging line (remove in production)
}

$total = 0;
?>
            <div class="container">
    <h1>Your Cart</h1>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>₱<?= number_format($row['price'], 2) ?></td>
                        <td>
    <form method="POST" action="update_quantity.php" style="display: flex; align-items: center;">
        <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
        
        <!-- Quantity Input -->
        <input type="number" name="quantity" value="<?= htmlspecialchars($row['quantity']) ?>" min="1" style="width: 50px; text-align: center; margin-right: 10px;">
        <td>₱<?= number_format($row['price'] * $row['quantity'], 2) ?></td>
        <!-- Update & Remove Buttons -->
        <td>
        <button type="submit" name="update" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer; margin-left: 5px;" class="fas fa-sync-alt"></button>
        
        <button type="submit" formaction="remove_from_cart.php" name="remove" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; margin-left: 5px;" class="fas fa-trash"></button>
                </td>
    </form>
</td>
                    </tr>
                    <?php $total += $row['price'] * $row['quantity']; ?>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p class="total">Total: ₱<?= number_format($total, 2) ?></p>
        <div class="button-container">
            <button onclick="window.location.href='/project/menu.php#healthy-drinks'">Back to Products</button>
            <form method="GET" action="/project/project-folder/php/customer_info.php">
                <button type="submit">Place Order</button>
            </form>
        </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
        <button onclick="window.location.href='/project/menu.php#healthy-drinks'">Back to Menu</button>
    <?php endif; ?>
</div>

    <script>
  let index = 0;
        const slides = document.querySelectorAll(".slide");
        function showSlide(n) {
            slides.forEach((slide, i) => {
                slide.style.display = i === n ? "block" : "none";
            });
        }
        function nextSlide() {
            index = (index + 1) % slides.length;
            showSlide(index);
        }
        function prevSlide() {
            index = (index - 1 + slides.length) % slides.length;
            showSlide(index);
        }
        setInterval(nextSlide, 5000);
        showSlide(index);
        window.addEventListener("scroll", function() {
            document.getElementById("navbar").classList.toggle("scrolled", window.scrollY > 50);
        });

        </script>

</body>
</html>
<?php
$conn->close();
?>
