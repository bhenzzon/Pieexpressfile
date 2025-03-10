<?php
include 'db_connect.php';

// Fetch products sorted by category
$sql = "SELECT * FROM products ORDER BY category, name";
$result = $conn->query($sql);

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[$row['category']][] = $row; // Group by category
}

// Define the manual category order
$manual_category_order = [
    "Healthy Pies",
    "Fruit Chiayakultea",
    "Lemon Chia",
    "Healthy Dimsums",
    "Healthy Sausages",
    "Healthy Treats",
    "Gift Box of 10",
    "Extras"
];

// Sort the $products array by keys (the category names) based on manual order
uksort($products, function($a, $b) use ($manual_category_order) {
    // Get the index of each category in the manual order array
    $posA = array_search($a, $manual_category_order);
    $posB = array_search($b, $manual_category_order);

    // If a category is not found, assign it a large index so it appears last
    if ($posA === false) {
        $posA = count($manual_category_order);
    }
    if ($posB === false) {
        $posB = count($manual_category_order);
    }
    return $posA - $posB;
});

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Pie Express</title>
    <link rel="icon" type="image/x-icon" href="/project/img/logo.png">

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet">
    <!-- Font Awesome JS -->
    <link rel="icon" type="image/x-icon" href="/project/img/logo.png">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
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
            z-index: 1000;
            height: 100px;
        }
        .logo img {
            width: 90px;
            height: auto;
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
        .category-section {
            background: white;
            margin: 30px auto;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
        }
        .category-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #014022;
            margin-bottom: 15px;
            border-bottom: 2px solid #014022;
            padding-bottom: 5px;
        }
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 10px;
        }
        .product {
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff;
            padding: 15px;
            text-align: center;
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
        .button {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        .button button {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
            border: none;
        }
        .update-btn {
            background-color: #014022;
            color: white;
        }
        .update-btn:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: red;
            color: white;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
        .notification-container {
    position: relative;
    font-size: 20px;
    color: white;
    cursor: pointer;
 
}
        .notif-badge {
    position: relative;
    top: -5px;
    right: 10px;
    background: red;
    color: white;
    font-size: 12px;
    font-weight: bold;
    padding: 3px 7px;
    border-radius: 50%;
}
.notification-container:hover {
    color: #FFC107; /* Mustard Yellow */
}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <a href="/project/project-folder/index.php"><img src="/project/img/logo.png" alt="logo"></a> 
        </div>
        <div class="nav-links">
            <a href="/project/project-folder/index.php" class="order-now">Home</a>
            <a href="/project/project-folder/php/load_products.php">Store</a>
            <a href="/project/project-folder/home.html">Add Item</a>
            <a href="/project/project-folder/php/orders.php">Orders</a>    
            <a href="/project/project-folder/php/order_history.php">History</a>     
            <a href="orders.php" class="notification-container">
                <i class="fas fa-bell"></i>
                <span id="notif-count" class="notif-badge">0</span>
            </a>  
        </div>
    </nav>

    <div style="margin-top: 120px;">
    <h1 style="text-align: center;">Our Products</h1>
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $category => $items): ?>
            <div class="category-section">
                <h2 class="category-title"><?php echo htmlspecialchars($category); ?></h2>
                <div class="product-container">
                    <?php foreach ($items as $product): ?>
                        <div class="product">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <p><strong>Price:</strong> ₱<?php echo number_format($product['price'], 2); ?></p>
                            <div class="button">
                                <form method="POST" action="update_item.php">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="update-btn">Update</button>
                                </form>
                                <form method="POST" action="delete_product.php" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; font-size:1.2em; color:#888;">No products available.</p>
    <?php endif; ?>
</div>
    <script>
         function fetchNewOrders() {
        $.ajax({
            url: "/project/project-folder/fetch_orders.php", // PHP file to get order count
            method: "GET",
            success: function(response) {
                let count = parseInt(response) || 0;
                $("#notif-count").text(count);

                if (count > 0) {
                    $("#notif-count").show();
                } else {
                    $("#notif-count").hide();
                }
            }
        });
    }

    // Fetch new orders every 10 seconds
    setInterval(fetchNewOrders, 10000);
    fetchNewOrders();
        </script>
</body>
</html>
