<?php
include 'db_connect.php'; // Ensure the database is connected

// Handle add-to-cart logic before displaying products
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($product_id && $quantity > 0) {
        // Check if product already exists in cart
        $sql_check = "SELECT id, quantity FROM cart WHERE product_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $product_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;
            $sql_update = "UPDATE cart SET quantity = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $new_quantity, $row['id']);
            $stmt_update->execute();
        } else {
            $sql_insert = "INSERT INTO cart (product_id, quantity) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ii", $product_id, $quantity);
            $stmt_insert->execute();
        }

        // Display success message
        echo "<div id='cart-message' class='success'>Added to cart successfully!</div>";

        $stmt_check->close();
        if (isset($stmt_update)) $stmt_update->close();
        if (isset($stmt_insert)) $stmt_insert->close();
    }
}

// Fetch the products grouped by category
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$custom_category_order = ['Healthy Pies', 'Fruit Chiayakultea', 'Lemon Chia', 'Healthy Dimsums', 'Healthy Sausages', 'Healthy treats', 'Gift Box of 10', 'Extras' ]; // Define your order
$categories = [];

// Organize products into categories
while ($row = $result->fetch_assoc()) {
    $categories[$row['category']][] = $row;
}

// Reorder categories based on custom order
$sorted_categories = [];
foreach ($custom_category_order as $category) {
    if (isset($categories[$category])) {
        $sorted_categories[$category] = $categories[$category];
        unset($categories[$category]); // Remove from the original list
}

// Append remaining categories in default order
$sorted_categories += $categories;
}
// Include CSS for fade animation
echo "<style>
    #cart-message {
        position: fixed;
        top: 13%;
        left: 50%;
        transform: translateX(-50%);
        background: #4CAF50;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        opacity: 1;
        transition: opacity 1s ease-in-out;
        z-index: 2;
        font-family: 'Poppins', sans-serif;
    }
</style>";

// JavaScript to fade out the message smoothly
echo "<script>
    setTimeout(function() {
        var message = document.getElementById('cart-message');
        if (message) {
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 1000);
        }
    }, 2000);
</script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu - Pie EXPRESS</title>
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
    /* Menu Page Content */
    .menu-container {
    margin-top: 20px; /* Adjust to prevent overlap with the fixed nav bar */
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center; /* Center content */
    gap: 20px; /* Space between categories */
}

.menu-category {
    width: 90%;
    max-width: 1500px;
    background-color: #fff;
    padding: 40px; /* Reduced padding for better balance */
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: left;
}

.menu-category h2 {
    margin-bottom: 20px;
    color: #013220;
    border-bottom: 3px solid #FFC107; /* Slightly thicker border */
    padding-bottom: 8px;
    font-size: 24px;
    font-weight: bold;
}

/* Product Container */
.product-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Responsive grid */
    gap: 20px;
}

/* Product Item */
.product {
    background: #f8f8f8;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease-in-out;
    max-width: 250px;
}

.product:hover {
    transform: scale(1.03);
}

.product img {
    width: 150px;
    height: 200px;
    object-fit: cover;
    border-radius: 5px;
}

.product h2 {
    font-size: 18px;
    margin: 10px 0;
    color: #333;
}

.product p {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}

/* Price Styling */
.product p strong {
    color: #E44D26;
    font-size: 16px;
}

/* Add to Cart Form */
.product form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}

.product input[type="number"] {
    width: 50px;
    padding: 5px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 5px;
}

/* Add to Cart Button */
.product button {
    background-color: #FFC107;
    color: #fff;
    border: none;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 5px;
    transition: background 0.3s ease-in-out;
}

.product button:hover {
    background-color: #e0a800;
}

/* Responsive Design */
@media (max-width: 768px) {
    .menu-category {
        padding: 30px;
        position: fixed;
        z-index: 2;
    }
    .product-container {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
}
    @media (max-width: 768px) {
      .menu-item {
        width: 45%;
      }
    }
    @media (max-width: 480px) {
      .menu-item {
        width: 90%;
      }
    }
    .photo-background {
                position: relative;
                width: 100%;
                min-height: 250px; /* Minimal height; adjust as needed */
                background: url('img/food-7.jpg') no-repeat center center; /* Replace with your actual image path */
                background-size: cover;
                }
                 .photo-background h2 {
                  position: absolute;
                top: 13%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: #FFC107;
                padding: 12px 20px;
                font-size: 70px;
                font-weight: 600;
                border-radius: 5px;
                text-decoration: none;
                z-index: 2; 
                font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
                }
                .photo-background .overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5); /* Lowered exposure overlay */
                z-index: 1;
                }

                .order-now-overlay2 {
                position: absolute;
                top: 70%;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: #FFC107;
                color: #013220;
                padding: 12px 20px;
                font-size: 18px;
                font-weight: 600;
                border-radius: 5px;
                text-decoration: none;
                z-index: 2; /* Ensures the button appears above the overlay */
                }
                .slider_wrap {
                margin-top: 10px; /* Reduced from 20px */
                margin-left: 20px; /* Moves Featured Food to the right */
            }

            /* Make "Featured Food" and "Our Menu" appear in a row */
            .row {
                display: flex;
                justify-content: space-between;
                align-items: flex-start; /* Align items to the top */
                flex-wrap: wrap; /* Ensure responsiveness */
            }

            .col-md-4 {
                flex: 1; /* Make it take space dynamically */
                max-width: 50%; /* Control width */
            }

            .col-md-8 {
                flex: 1;
                max-width: 55%;
            }

            /* Adjust image alignment in the service slider */
            #service-slider .image {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-bottom: 5px; /* Reduced space */
            }

            /* Increase the size of the image in the photo carousel */
            #service-slider img {
                max-width: 300px; /* Increased from 120px */
                height: auto;
                border-radius: 10px;
            }

            /* Align text with images */
            #service-slider h3, 
            #service-slider p {
                text-align: center;
                margin: 5px 0;
            }

            .heading {
                font-size: 26px;
                color: #013220; /* Original color kept */
            }

            .heading_space {
                width: 50px;
                border-top: 2px solid #FFC107; /* Original color kept */
                margin-bottom: 20px;
            }

            .branch_widget {
                list-style: none;
                padding: 0;
                display: flex;
                flex-wrap: wrap; /* Ensures responsiveness */
                justify-content: flex-start; /* Aligns items to the left */
                gap: 20px; /* Space between branches */
            }

            .branch_widget li {
                font-size: 18px;
                color: #333;
                display: flex;
                align-items: left;
                background-color: #f9f9f9;
                padding: 10px 150px;
                border-radius: 8px;
                width: 100%;
                max-width: 600px; /* Limits width per row item */
                justify-content: flex-start; /* Aligns text inside the item to the left */
            }

            .branch_widget li a {
                display: flex;
                align-items: center;
                text-decoration: none;
                color: #013220;
                font-weight: bold;
                justify-content: flex-start; /* Aligns content inside the link to the left */
            }

            .location-icon {
                width: 20px;
                height: auto;
                margin-right: 20px; /* Space between icon and text */
                align-items: flex-start;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .branch_widget {
                    flex-direction: column; /* Stack items on small screens */
                    gap: 10px;
                }

                .branch_widget li {
                    max-width: 100%;
                }
            }
              #footer {
                background-color: #013220;
                color: #fff;
                padding: 40px 20px;
                text-align: center;
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
              .chatbox {
                width: 400px;
                height: 450px;
                border: 1px solid #ccc;
                display: none;
                flex-direction: column;
                justify-content: space-between;
                position: fixed;
                top: 40%;
                right: 20px;
                background: white;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                z-index: 2;
            }
            .chatbox-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px;
                background: darkgreen;
                color: white;
                font-weight: bold;
                border-radius: 10px;
            }
            .close-button {
                background: none;
                border: none;
                color: white;
                font-size: 16px;
                cursor: pointer;
            }
            .messages {
                flex: 1;
                padding: 10px;
                overflow-y: auto;
            }
            .input-box {
                display: flex;
                padding: 10px;
                border-top: 1px solid #ccc;
            }
            .input-box input {
                flex: 1;
                padding: 5px;
            }
            .input-box button {
                padding: 5px 10px;
                background: darkgreen;
                color: white;
                border: none;
                cursor: pointer;
            }
            .chat-button {
                position: fixed;
                top: 90%;
                right: 20px;
                background: darkgreen;
                color: white;
                padding: 10px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                z-index: 2;
                
            }
            .suggested-messages {
                padding: 10px;
                border-top: 1px solid #ccc;
                background: #f1f1f1;
                
            }
            .suggested-messages button {
                background: lightgray;
                border: none;
                padding: 5px 15px;
                margin: 2px;
                cursor: pointer;
                border-radius: 10px;
            }
            .message-container {
                display: flex;
                flex-direction: column;
                 border-radius: 10px;         
            }
            .user-message {
                justify-self: flex-end;
                background: lightgreen;
                padding: 5px;
                border-radius: 5px;
                margin: 30px 100px;
                display: flex;
                margin-right: -5px;
            }
            .bot-message {
                 justify-self: flex-start;
                background: lightgray;
                padding: 5px;
                border-radius: 5px;
                margin: 15px 0;
                margin-right: 50px;
                display: flex;
            }
            .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .product {
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            background: #fff;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .product img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .product h2 {
            font-size: 1.2em;
            margin: 10px 0;
            color: #333;
        }
        .product p {
            font-size: 0.9em;
            color: #555;
        }
        .product button {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        .product button:hover {
            background-color: #218838;
        }
        .product input[type='number'] {
            width: 50px;
            padding: 5px;
            margin: 5px 0;
        }
    #imagePreview {
        max-width: 80px;  /* Adjust image size */
        max-height: 80px;
        display: none;
        border-radius: 5px;
        margin: 10px auto;  /* Center inside the form */
        display: block;
    }
    .category-buttons {
      text-align: center;
      margin: 20px 0;
      position: -webkit-sticky; /* For Safari */
      position: sticky;
      top: 10%;             /* Sticks at the top */
      z-index: 2;         /* Ensures it's above other elements */  /* Optional: background to cover content underneath */
      padding: 10px 0;    /* Optional: space around buttons */
  }

  /* Style the buttons */
  .category-buttons button {
      background-color: #013220;
      color: #fff;
      border: none;
      padding: 10px 20px;
      margin: 5px;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
  }

  .category-buttons button:hover {
      background-color: #026640;
  }
  
  </style>
</head>
<body>
  <!-- Nav Bar -->
  <nav class="navbar" id="navbar">
        <div class="logo">
            <a class="navbar-brand" href="index.html"><img src="img/logo.png" alt="logo" class="img-responsive"></a> 
        </div>
        <div class="nav-links">
            <a href="index.html">Home</a>
            <a href="#healthy-drinks" class="order-now">Order Now</a>
            <a href="#healthy-drinks">Menu</a>
            <a href="#services">Delivery</a>
            <a href="#about-us">About Us</a>
            <a href="#footer">Contacts</a>
            <a href="project-folder/php/order_placed.php">My Purchases</a>
            <a href="project-folder/php/checkout.php" class="fas fa-shopping-cart"></a>
            
        </div>
    </nav>
  <div class="carousel">
        <button class="prev" onclick="prevSlide()">&#10094;</button>
        <div class="carousel-container" id="carousel">
            <div class="slide active">
                <img src="img/bg1.jpg">
                <div class="carousel-text left">
                    <h1>PIE EXPRESS</h1>
                    <p>Making a healthier Philippines, One kuchay Pie at a time!<br>TO OUR HEALTH REVOLUTION!</p>
                    <a href="https://pieexpressph.storehub.me/" class="order-now-overlay">Order Now</a> <!-- Added Button -->
                </div>
            </div>
            <div class="slide">
                <img src="img/bg2.JPG">
                <div class="carousel-text right">
                    <h1>PIE EXPRESS</h1>
                    <p>Making a healthier Philippines.</p>
                    <a href="https://pieexpressph.storehub.me/" class="order-now-overlay">Order Now</a> <!-- Added Button -->
                </div>
            </div>
            <div class="slide">
                <img src="img/bg4.jpg">
                <div class="carousel-text left">
                    <h1>PIE EXPRESS</h1>
                    <p>Bringing sustainable, healthy, and good quality products to all.</p>
                    <a href="https://pieexpressph.storehub.me/" class="order-now-overlay">Order Now</a> <!-- Added Button -->
                </div>
            </div>
            <div class="slide">
                <img src="img/bg3.jpg">
                <div class="carousel-text right">
                    <h1>PIE EXPRESS</h1>
                    <p>Customers deserve the healthy and best quality products and service.<br>Compassion for PIE EXPRESS and to its employees.</p>
                    <a href="https://pieexpressph.storehub.me/" class="order-now-overlay">Order Now</a> <!-- Added Button -->
                </div>
            </div>
        </div>
        <button class="next-btn" onclick="nextSlide()">&#10095;</button>
            </div>

            <button class="chat-button" onclick="toggleChatbox()">Chat</button>
        <div class="chatbox" id="chatbox">
            <div class="chatbox-header">
                Pie Express
                <button class="close-button" onclick="toggleChatbox()">---</button>
            </div>
            <div class="messages" id="messages"></div>
            <div class="suggested-messages">
                <button onclick="quickMessage('Hello')">Hello</button>
                <button onclick="quickMessage('How to order?')">How to order?</button>
                <button onclick="quickMessage('I want to order')">I want to order</button>
                <button onclick="quickMessage('Price')">Price</button>
            </div>
            <div class="input-box">
                <input type="text" id="userInput" placeholder="Type a message...">
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>

  <!-- Menu Listing -->
  <div class="menu-container">
  <h2>OUR PRODUCTS</h2>

  <?php
  include 'db_connect.php'; // Ensure you have the correct database connection file

  $sql = "SELECT * FROM products ORDER BY category";
  $result = $conn->query($sql);

  $categories = [];
  while ($row = $result->fetch_assoc()) {
      // Group products by category
      $categories[$row['category']][] = $row;
  }
  ?>

  <!-- Category Navigation Buttons (above the menu sections) -->
  <?php if (!empty($categories)) : ?>
  <div class="category-buttons">
    <?php 
    foreach ($categories as $category => $products) {
        $categoryId = strtolower(str_replace(' ', '-', $category));
        echo "<button onclick=\"scrollToCategory('{$categoryId}')\">" . ucfirst($category) . "</button>";
    }
    ?>
  </div>
<?php endif; ?>

  <!-- Display each category section -->
  <?php
  if (!empty($sorted_categories)) {
    foreach ($sorted_categories as $category => $products) {
        echo "<section class='menu-category' id='" . strtolower(str_replace(' ', '-', $category)) . "'>";
        echo "<h3>" . ucfirst($category) . "</h3>";
        echo "<div class='menu-items'>";
        echo "<div class='product-container'>";

        foreach ($products as $product) {
            echo "
            <div class='product'>
                <img src='" . $product['image_url'] . "' alt='" . $product['name'] . "'>
                <h2>" . $product['name'] . "</h2>
                <p>" . $product['description'] . "</p>
                <p><strong>Price:</strong> ₱" . $product['price'] . "</p>
                <form method='POST' action='menu.php#" . strtolower(str_replace(' ', '-', $category)) . "'>
                    <input type='hidden' name='product_id' value='" . $product['id'] . "'>
                    <label for='quantity'>Qty:</label>
                    <input type='number' name='quantity' value='1' min='1'>
                    <button type='submit'>Add to Cart</button>
                </form>
            </div>";
        }

        echo "</div></section>";
    }
} else {
    echo "<p style='text-align:center; font-size:1.2em; color:#888;'>No products available.</p>";
}
  
  $conn->close();
  ?>

</div>

  <div class="photo-background" id="ordernow">
              <div class="overlay"></div>
              <h2>Try our deals!</h2>
              <a href="https://pieexpressph.storehub.me/" class="order-now-overlay2">Order Now</a>
            </div>

            <section id="services" class="padding-bottom">
            <div class="container">
              <div class="row">
                <!-- Carousel Section: Left Side -->
                <div class="col-md-4">
                   <h2 class="heading">Our Store Locations</h2>
                   <hr class="heading_space">
                   <div class="slider_wrap">
                     <div id="service-slider" class="owl-carousel">
                       <div class="item">
                         <div class="item_inner">
                           <div class="image">
                             <img src="img/loc1.jpg" alt="Featured Food 1">
                           </div>
                           <h3>Our Location</h3>
                           <p>Enjoy Delicious Food!</p>
                         </div>
                       </div>
                       <div class="item">
                         <div class="item_inner">
                           <div class="image">
                             <img src="img/loc2.jpg" alt="Featured Food 2">
                           </div>
                           <h3>Shell SLEX</h3>
                           <p>Enjoy Delicious Food!</p>
                         </div>
                       </div>
                       <div class="item">
                         <div class="item_inner">
                           <div class="image">
                             <img src="img/loc3.jpg" alt="Featured Food 3">
                           </div>
                           <h3>Pamplona 3</h3>
                           <p>Enjoy Delicious Food!</p>
                         </div>
                       </div>
                       <div class="item">
                         <div class="item_inner">
                           <div class="image">
                             <img src="img/loc4.jpg" alt="Featured Food 4">
                           </div>
                           <h3>SM Southmall</h3>
                           <p>Enjoy Delicious Food!</p>
                         </div>
                       </div>
                       <div class="item">
                         <div class="item_inner">
                           <div class="image">
                             <img src="img/loc5.jpg" alt="Featured Food 4">
                           </div>
                           <h3>Festival Mall</h3>
                           <p>Enjoy Delicious Food!</p>
                         </div>
                       </div>
                     </div>
                   </div>
                </div>
          
                <!-- Menu Section: Right Side -->
                <div class="col-md-8">
    <h2 class="heading" class="fas">Google Map</h2>
    <hr class="heading_space">
    <ul class="branch_widget">
        <li>
            <a href="https://www.google.com/maps/search/Festival+Mall+Alabang+Muntinlupa/@14.4175463,121.0385127,17z/data=!3m1!4b1?entry=ttu&g_ep=EgoyMDI1MDIxMS4wIKXMDSoJLDEwMjExNDUzSAFQAw%3D%3D" target="_blank">
                <img src="img/location-icon.png" alt="Location" class="location-icon">
                Pie Express - Festival Mall Alabang Muntinlupa
            </a>
        </li>
        <li>
            <a href="https://www.google.com/maps/place/Shell/@14.3943212,121.0022968,14z/data=!4m10!1m2!2m1!1sShell+South+Luzon+Expressway+Muntinlupa!3m6!1s0x3397d0ff47062607:0x9011ca0196c8d9b7!8m2!3d14.3943236!4d121.0383469!15sCidTaGVsbCBTb3V0aCBMdXpvbiBFeHByZXNzd2F5IE11bnRpbmx1cGEiA4gBAVopIidzaGVsbCBzb3V0aCBsdXpvbiBleHByZXNzd2F5IG11bnRpbmx1cGGSAQtnYXNfc3RhdGlvbuABAA!16s%2Fg%2F11bc7lw6x3?entry=ttu&g_ep=EgoyMDI1MDIxMS4wIKXMDSoJLDEwMjExNDUzSAFQAw%3D%3D" target="_blank">
                <img src="img/location-icon.png" alt="Location" class="location-icon">
                Pie Express - Shell South Luzon Expressway Muntinlupa
            </a>
        </li>
        <li>
            <a href="https://www.google.com/maps/place/SM+Southmall/@14.4332666,121.0079916,17z/data=!3m1!4b1!4m6!3m5!1s0x3397d1dd991a126b:0x67862091bd5d31e2!8m2!3d14.4332614!4d121.0105665!16s%2Fm%2F02z5cjy?authuser=0&hl=en&entry=ttu&g_ep=EgoyMDI1MDIxMS4wIKXMDSoJLDEwMjExNDUzSAFQAw%3D%3D" target="_blank">
                <img src="img/location-icon.png" alt="Location" class="location-icon">
                Pie Express - SM South mall Las Pinas
            </a>
        </li>
        <li>
            <a href="https://maps.app.goo.gl/dSGxsh2vQWDBUdm46" target="_blank">
                <img src="img/location-icon.png" alt="Location" class="location-icon">
                Pie Express - Pamplona 3 Las Pinas
            </a>
        </li>
        <li>
            <a href="https://maps.app.goo.gl/ToaVZSdWmBQbzyuT7" target="_blank">
                <img src="img/location-icon.png" alt="Location" class="location-icon">
                Pie Express - Muntinlupa City Hall Canteen
            </a>
        </li>
    </ul>
</div>
              </div>
            </div>
          </section>

  <footer id="footer">
              <div class="footer-container">
                <div class="contact-info">
                  <p><i class="fas fa-phone-alt"></i> 0961 625 3718</p>
                  <p><i class="fas fa-envelope"></i> customercare@pieexpressph.com</p>
                  <p><i class="fas fa-clock"></i> Working Hours: 9am - 5pm</p>
                </div>

                <div class="feedback">
                    <h3>Feedback</h3>
                    <form id="feedbackForm" enctype="multipart/form-data">
                        <input type="text" name="name" placeholder="Your Name" required>    
                        <input type="email" name="email" placeholder="Your Email" required>
                        <textarea name="message" placeholder="Your Feedback" required></textarea>

                        <!-- Image Upload Input -->
                        <input type="file" name="image" accept="image/*" id="imageInput" required>

                        <!-- Image Preview -->
                        <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 80px; max-height: 80px; display: none; border-radius: 5px;">

                        <button type="submit">Submit</button>
                    </form>

                    <!-- Success/Error Message -->
                    <p id="feedbackMessage" style="color: yellow; font-weight: bold; display: none;"></p>
                </div>
                <div class="social-media">
                  <a href="https://www.facebook.com/pieexpressph" target="_blank"><i class="fab fa-facebook-f"></i></a>
                  <a href="https://www.instagram.com/pieexpressph?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank"><i class="fab fa-instagram"></i></a>
                  <a href="www.pieexpressph.com" target="_blank"><i class="fas fa-globe"></i></a>
                </div>
              </div>
            </footer>

          
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

        $(document).ready(function () {
        console.log("Document ready. Initializing carousel..."); // Debugging line
        $("#service-slider").owlCarousel({
            items: 1,
            autoplay: true,
            loop: true,
            margin: 10,
            nav: true,
            navText: ["&#10094;", "&#10095;"], // Adds navigation arrows
            dots: true // Enables pagination dots
        });
        console.log("Carousel initialized."); // Debugging line
         });

         function toggleChatbox() {
            let chatbox = document.getElementById('chatbox');
            chatbox.style.display = chatbox.style.display === 'flex' ? 'none' : 'flex';
        }

        function sendMessage() {
            let input = document.getElementById('userInput');
            let message = input.value.trim();
            if (message !== '') {
                appendMessage("You", message, "user-message");
                input.value = '';
                setTimeout(() => botReply(message), 1000);
            }
        }

        function quickMessage(text) {
            document.getElementById('userInput').value = text;
            sendMessage();
        }

        function botReply(userMessage) {
            let responses = {
                "hello": "Hi there! How can I help you?",
                "how to order?": "You can place an order through our website! https://pieexpressph.storehub.me/",
                "i want to order": "You can place an order through our website! https://pieexpressph.storehub.me/",
                "order": "You can place an order through our website! https://pieexpressph.storehub.me/",
                "price": "Our prices vary depending on the item. Check our menu!",
                "thanks": "It's our pleasure to serve you.",
                "default": "I'm not sure about that. Can you rephrase?"
            };
            
            let reply = responses[userMessage.toLowerCase()] || responses["default"];
            appendMessage("Pie", reply, "bot-message");
        }

        function appendMessage(sender, message, className) {
            let messagesDiv = document.getElementById('messages');
            let newMessage = document.createElement('div');
            newMessage.className = className;
            newMessage.textContent = sender + ": " + message;
            messagesDiv.appendChild(newMessage);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
        function scrollToCategory(categoryId) {
    const categorySection = document.getElementById(categoryId);
    if (categorySection) {
      categorySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

        document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("feedbackForm");
    const imageInput = document.getElementById("imageInput");
    const imagePreview = document.getElementById("imagePreview");
    const feedbackMessage = document.getElementById("feedbackMessage");

    // Preview the selected image
    imageInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = "block";
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = "none";
        }
    });

    // Handle Form Submission via AJAX
    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent normal form submission

        var formData = new FormData(this);

        fetch("feedback.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            feedbackMessage.textContent = data; // Display success message
            feedbackMessage.style.color = "green";
            feedbackMessage.style.display = "block"; // Show message

            form.reset();
            imagePreview.style.display = "none"; // Hide image preview
        })
        .catch(error => {
            feedbackMessage.textContent = "Error submitting feedback.";
            feedbackMessage.style.color = "red";
            feedbackMessage.style.display = "block";
        });
    });
});
      

    </script>
</body>
</html>
