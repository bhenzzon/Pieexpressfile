<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Fetch product details
    $sql = "SELECT * FROM products WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

if (!$product) {
    echo "<script>alert('Product not found!'); window.location.href='load_products.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Product - Pie Express</title>
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
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
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
    transition: background-color 0.3s ease;
}

.nav-links a:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.order-now {
    background-color: #FFC107;
    color: #013220;
    padding: 10px 15px;
    border-radius: 5px;
}

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    padding-top: 100px;
}

.form-container {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 400px;
    text-align: center;
}

h2 {
    color: #013220;
    font-weight: 600;
}

form label {
    display: block;
    font-size: 14px;
    font-weight: bold;
    margin: 10px 0 5px;
    text-align: left;
}

form input[type="text"],
form input[type="number"],
form textarea {
    width: 90%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

form textarea {
    resize: none;
    height: 80px;
}

form button,
form input[type="submit"] {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #013220;
    color: white;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 20px
}

form button:hover,
form input[type="submit"]:hover {
    background-color: black;
}

.image-preview {
    display: flex;
    justify-content: center;
    margin: 10px 0;
}

.image-preview img {
    max-width: 100px;
    border-radius: 5px;
}

.back-button {
    display: block;
    margin-top: 15px;
    padding: 10px;
    background-color: rgba(1, 50, 32, 1);
    color: white;
    text-align: center;
    text-decoration: none;
    font-size: 16px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.back-button:hover {
    background-color: black;
}
    </style>
<body>
    <nav class="navbar">
        <div class="logo">
            <a href="/project/project-folder/index.php">
                <img src="/project/img/logo.png" alt="logo">
            </a>
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
    <div class="form-container">
        <h2>Update Product</h2>
        <form action="save_update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

            <label>Product Name:</label>
            <input type="text" name="name" value="<?php echo $product['name']; ?>" required>

            <label>Description:</label>
            <textarea name="description"><?php echo $product['description']; ?></textarea>

            <label>Price:</label>
            <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>

            <label>Category:</label>
            <select name="category" required>
                <?php
                // Fetch available categories from the database
                $sql = "SELECT DISTINCT category FROM products";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $selected = ($row['category'] == $product['category']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($row['category']) . "' $selected>" . htmlspecialchars($row['category']) . "</option>";
                }
                ?>
            </select>

            <label>Current Image:</label>
            <div class="image-preview">
                <img src="<?php echo $product['image_url']; ?>" alt="Current Product Image">
            </div>

            <label>Change Image:</label>
            <input type="file" name="image">

            <input type="submit" value="Update">
        </form>

        <a href="load_products.php" class="back-button">Back to Products</a>
    </div>
</div>
</body>
</html>