<?php
include 'db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['image']['name']);

        // Define first target directory for the user panel
        $target_dir_user = "/project/project-folder/uploads/gallery/"; 
        $target_file_user = $target_dir_user . $image_name;

        // Define second target directory for the admin panel
        $target_dir_admin = "../uploads/gallery/"; 
        $target_file_admin = $target_dir_admin . $image_name;

        // Ensure both directories exist
        if (!file_exists($target_dir_user)) {
            mkdir($target_dir_user, 0777, true);
        }
        if (!file_exists($target_dir_admin)) {
            mkdir($target_dir_admin, 0777, true);
        }

        // Move the uploaded file to the user panel directory
        $upload_user = move_uploaded_file($_FILES['image']['tmp_name'], $target_file_user);
        // Copy the file from the user directory to the admin directory
        $upload_admin = copy($target_file_user, $target_file_admin);

        if ($upload_user && $upload_admin) {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $category = $_POST['category'];  // Get category value
            $price = $_POST['price'];
        
            $image_url = $target_file_user;
        
            $sql = "INSERT INTO products (name, description, category, price, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssds", $name, $description, $category, $price, $image_url);
        
            if ($stmt->execute()) {
                $message = "Product added successfully!";
            } else {
                $message = "Error: " . $conn->error;
            }
            $stmt->close();
        } else {
            $message = "Failed to upload the image.";
        }
    } else {
        $message = "Image upload error: " . $_FILES['image']['error'];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Upload - Pie Express</title>
  <link rel="icon" type="image/x-icon" href="/project/img/logo.png">
  <!-- Include any external stylesheets or scripts here -->
  <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .container {
        width: 90%;
        max-width: 500px;
        background-color: #ffffff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    h1 {
        font-size: 1.8rem;
        color: #333;
        text-align: center;
        margin-bottom: 20px;
        font-family: Verdana, sans-serif;
    }
    .message-container {
        text-align: center;
        margin-bottom: 20px;
        font-size: 1rem;
        padding: 10px;
        border-radius: 5px;
        background-color: #e7ffe7;
        color: #28a745;
        border: 1px solid #28a745;
    }
    form {
        display: flex;
        flex-direction: column;
    }
    label {
        font-size: 1rem;
        color: #444;
        margin-bottom: 5px;
    }
    input[type="text"],
    input[type="number"],
    input[type="file"],
    textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        font-size: 1rem;
        color: #333;
    }
    input[type="text"]:focus,
    input[type="number"]:focus,
    textarea:focus {
        outline: none;
        border-color: #555;
    }
    .button {
        width: 100%;
        background-color: rgba(1, 50, 32, 1);
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 10px;
    }
    .button:hover {
        background-color: #555;
    }
    .btn-container {
        text-align: center;
        margin-top: 20px;
    }
    .btn-container a {
        display: inline-block;
        background-color: rgba(1, 50, 32, 1);
        color: #fff;
        padding: 10px 0;
        text-decoration: none;
        border-radius: 5px;
        margin: 1px;
        transition: background-color 0.3s ease;
    }
    .btn-container a:hover {
        background-color: #555;
    }

    .category {
        width: 200px;
        padding: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
      <h1>Upload Product</h1>
      <?php if (!empty($message)) { ?>
          <div class="message-container">
              <?php echo $message; ?>
          </div>
      <?php } ?>
      <!-- Product Upload Form -->
      <form action="add_image.php" method="POST" enctype="multipart/form-data">
          <label for="name">Product Name:</label>
          <input type="text" name="name" required>
  
          <label for="description">Product Description:</label>
          <textarea name="description" rows="4" required></textarea>

          <label for="category">Category:</label>
            <select class= "category" name="category" required>
                <option value="Healthy Pies">Healthy Pies</option>
                <option value="Fruit Chiayakultea">Fruit Chiayakultea</option>
                <option value="Lemon Chia">Lemon Chia</option>
                <option value="Healthy Dimsums">Healthy Dimsums</option>
                <option value="Healthy Sausages">Healthy Sausages</option>
                <option value="Healthy Treats">Healthy Treats</option>
                <option value="Gift Box of 10">Gift Box of 10</option>
                <option value="Extras">Extras</option>
            </select>
  
          <label for="price">Price:</label>
          <input type="number" name="price" step="0.01" required>
  
          <label for="image">Product Image:</label>
          <input type="file" name="image" accept="image/*" required>
  
          <input type="submit" value="Add Product" class="button">
      </form>
  
      <!-- Navigation Buttons -->
      <div class="btn-container">
          <a href="load_products.php" class="button">Show Products</a>
          <a href="../home.html" class="button">Back to Homepage</a>
      </div>
  </div>
</body>
</html>
