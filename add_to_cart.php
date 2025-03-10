<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CART - Pie Express</title>
    <link rel="icon" type="image/x-icon" href="/project/img/logo.png">
    <link rel="stylesheet" href="styles.css"> <!-- External CSS -->
    <script defer src="script.js"></script> 
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    /* General Reset */
    body, html {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #f0f4f7;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    /* Container Styling */
    .container {
        text-align: center;
        padding: 20px;
        border: 2px solid gray;
        border-radius: 10px;
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        max-width: 600px;
        width: 90%;
    }

    /* Success Message */
    .success {
        font-size: 1.2rem;
        margin: 10px 0;
        background-color: white;
        color: #013220;
        border: 1px solid white;
        font-family: verdana;
    }

    /* Error Message */
    .error {
        color: #ff4d4d;
        font-size: 1.2rem;
        margin: 10px 0;
         }

    /* Button Container */
        .button-container {
            justify-content: space-between 50px;
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
</style>
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
            <a href="/project/project-folder/php/checkout.php" class="fas fa-shopping-cart"></a>
            
        </div>
    </nav>
    <div class="container">
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include 'db_connect.php';

            $product_id = $_POST['product_id'] ?? null;
            $quantity = $_POST['quantity'] ?? null;

            if ($product_id && $quantity) {
                // Check if the product is already in the cart
                $sql_check = "SELECT id, quantity FROM cart WHERE product_id = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("i", $product_id);
                $stmt_check->execute();
                $result = $stmt_check->get_result();

                if ($result->num_rows > 0) {
                    // Update quantity
                    $row = $result->fetch_assoc();
                    $new_quantity = $row['quantity'] + $quantity;

                    $sql_update = "UPDATE cart SET quantity = ? WHERE id = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("ii", $new_quantity, $row['id']);
                    $stmt_update->execute();
                    echo "<p class='success'>Item added to the cart successfully!</p>";
                } else {
                    // Insert into cart
                    $sql_insert = "INSERT INTO cart (product_id, quantity) VALUES (?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("ii", $product_id, $quantity);
                    if ($stmt_insert->execute()) {
                        echo "<p class='success'>Item added to cart successfully!</p>";
                    } else {
                        echo "<p class='error'>Error: " . $conn->error . "</p>";
                    }
                }
                // Close connections
                $stmt_check->close();
                if (isset($stmt_update)) $stmt_update->close();
                if (isset($stmt_insert)) $stmt_insert->close();
            } else {
                echo "<p class='error'>Error: Missing product ID or quantity.</p>";
            }

            $conn->close();
        } else {
            echo "<p class='error'>Invalid request method.</p>";
        }
        ?>
        <div class="button-container">
            <button onclick="window.location.href='/project/menu.php#healthy-drinks'">Add More</button>
            <button onclick="window.location.href='checkout.php'">View Cart</button>
        </div>
    </div>
    
</body>
</html>
