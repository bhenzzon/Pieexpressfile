<?php
session_start();
$connection = new mysqli("localhost", "root", "", "ordering_system");
$_SESSION['previous_page'] = 'your_orders.php'; 

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if cart is empty
$result = $connection->query("SELECT * FROM cart");
if ($result->num_rows == 0) {
    die("Your cart is empty! <a href='javascript:history.back()'>Go back</a>");
}

// Check if customer info is already stored in session
if (isset($_SESSION['customer_name'], $_SESSION['customer_contact'], $_SESSION['customer_address'])) {
    $name = $_SESSION['customer_name'];
    $contact = $_SESSION['customer_contact'];
    $address = $_SESSION['customer_address'];
} else {
    $name = $contact = $address = ""; // Empty fields if no session data
}

// Handle customer info submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method']; // Get the selected payment method

    // File upload handling (Only for Online Payment)
    $image_name = NULL;
    if ($payment_method === "Online Payment" && isset($_FILES["payment_screenshot"]) && $_FILES["payment_screenshot"]["error"] === 0) {
        $image = $_FILES["payment_screenshot"];
        $target_dir = "/project/project-folder/php/uploads/gallery/"; // Ensure this folder exists
        $image_name = time() . "_" . basename($image["name"]);
        $target_file = $target_dir . $image_name;

        if (!is_dir($target_dir)) {
          mkdir($target_dir, 0755, true);
      }

        if (!move_uploaded_file($image["tmp_name"], $target_file)) {
            die("File upload failed! Error code: " . $_FILES["payment_screenshot"]["error"]);
        }
    }

    // Store customer info in session
    $_SESSION['customer_name'] = $name;
    $_SESSION['customer_contact'] = $contact;
    $_SESSION['customer_address'] = $address;

    // Insert customer order (with or without image)
    $stmt = $connection->prepare("INSERT INTO orders (customer_name, contact, address, order_status, payment_status, payment_method, payment_screenshot) 
                                  VALUES (?, ?, ?, 'Pending', 'Unpaid', ?, ?)");
    $stmt->bind_param("sssss", $name, $contact, $address, $payment_method, $image_name);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Retrieve cart items and insert into order_items
        $cart_items = $connection->query("SELECT cart.product_id, cart.quantity, products.name, products.price FROM cart JOIN products ON cart.product_id = products.id");

        while ($item = $cart_items->fetch_assoc()) {
            $product_id = $item['product_id'];
            $product_name = $item['name'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $stmt_items = $connection->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
            $stmt_items->bind_param("iisid", $order_id, $product_id, $product_name, $quantity, $price);
            $stmt_items->execute();
            $stmt_items->close();
        }

        // Clear cart after order placement
        $connection->query("DELETE FROM cart");

        // Redirect to order confirmation
        header("Location: your_orders.php?order_id=" . $order_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Your Details</title>
    <link rel="icon" type="image/x-icon" href="/project/img/logo.png">
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
    body {
    font-family: 'Poppins', sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background-color: #f8f8f8;
}

.container {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
        margin-top: 25%;
}
    h2 {
        color: #006400;
    }
    input {
        width: 95%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    button {
        background-color: #FFD700;
        color: #006400;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }
    button:hover {
        background-color: #DAA520;
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
    #footer {
                background-color: #013220;
                color: #fff;
                padding: 40px 20px;
                text-align: center;
                margin-top: 10%;
                width: 100%;
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
              .container {
        display: flex;
        justify-content: center;
        align-items: start;
        gap: 40px;
        max-width: 800px;
        margin: auto;
    }

    .form-container {
        flex: 1;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-top: 65%;
    }

    .qr-upload-container {
        flex: 1;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        display: none;
        text-align: center;
        margin-top: 65%;
    }

    .qr-upload-container img {
        width: 200px;
        margin-bottom: 10px;
    }

    #error_message {
        color: red;
        display: none;
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
            <a href="<?= $backLink ?>">My Purchases</a>
            <a href="/project/project-folder/php/checkout.php" class="fas fa-shopping-cart"></a>
            
        </div>
    </nav>
    <div class="container">
    <!-- Left Side: Customer Details Form -->
    <div class="form-container">
        <h2>Enter Your Details</h2>
        <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>

            <label>Contact:</label>
            <input type="text" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required><br><br>

            <label>Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" required><br><br>

            <label>Payment Method:</label>
            <select name="payment_method" id="payment_method" onchange="toggleQRUpload()" required>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="Online Payment">Online Payment</option>
            </select><br><br>

            <button type="submit">Confirm Order</button>
        </form>
    </div>

    <!-- Right Side: QR Code Display & Payment Upload (Hidden by Default) -->
    <div class="qr-upload-container" id="qr_upload_section">
        <h3>Scan the QR Code to Pay</h3>
        <img src="/project/project-folder/images/qr.png" alt="QR Code">
        <h4>Upload Payment Screenshot:</h4>
        <input type="file" id="payment_screenshot" name="payment_screenshot" accept="image/*">
        <p id="error_message">Please upload a payment screenshot.</p>
    </div>
</div>



        <!-- Right Side: QR Code Display & Payment Upload (Hidden by Default) -->
        <div class="qr-upload-container" id="qr_upload_section" style="display: none;">
            <h3>Scan the QR Code to Pay</h3>
            <img src="/project/project-folder/images/qr.png" alt="QR Code" width="200">  <!-- Replace with actual QR code path -->
            <h4>Upload Payment Screenshot:</h4>
            <input type="file" name="payment_screenshot" accept="image/*" required>
        </div>
    </div>
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
            <script>
function toggleQRUpload() {
    var paymentMethod = document.getElementById("payment_method").value;
    var qrSection = document.getElementById("qr_upload_section");
    var screenshotInput = document.getElementById("payment_screenshot");
    var errorMessage = document.getElementById("error_message");

    if (paymentMethod === "Online Payment") {
        qrSection.style.display = "block";
        screenshotInput.required = true;
    } else {
        qrSection.style.display = "none";
        screenshotInput.required = false;
        errorMessage.style.display = "none"; // Hide error if switching back
    }
}

function validateForm() {
    var paymentMethod = document.getElementById("payment_method").value;
    var screenshotInput = document.getElementById("payment_screenshot");
    var errorMessage = document.getElementById("error_message");

    if (paymentMethod === "Online Payment" && screenshotInput.files.length === 0) {
        errorMessage.style.display = "block";
        return false; // Prevent form submission
    }
    return true;
}
</script>

</html>