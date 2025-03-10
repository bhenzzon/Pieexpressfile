<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category']; // Get category from the form
    $image_url = '';

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['image']['name']);
        
        // Define two target directories
        $target_dir1 = "/project/project-folder/uploads/gallery/";
        $target_dir2 = "../uploads/gallery/";
        
        $target_file1 = $target_dir1 . $image_name;
        $target_file2 = $target_dir2 . $image_name;

        // Ensure both directories exist
        if (!file_exists($target_dir1)) {
            mkdir($target_dir1, 0777, true);
        }
        if (!file_exists($target_dir2)) {
            mkdir($target_dir2, 0777, true);
        }

        // Move uploaded file to both directories
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file1)) {
            copy($target_file1, $target_file2); // Copy file to second directory

            $image_url = "/project/project-folder/uploads/gallery/" . $image_name; // Save relative path
        } else {
            echo "<script>alert('Failed to upload image.'); window.history.back();</script>";
            exit;
        }
    } else {
        // Keep the old image if no new image is uploaded
        $query = "SELECT image_url FROM products WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $image_url = $product['image_url'];
        $stmt->close();
    }

    // Update the product details in the database, including category
    $sql = "UPDATE products SET name=?, description=?, price=?, category=?, image_url=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssi", $name, $description, $price, $category, $image_url, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='load_products.php';</script>";
    } else {
        echo "<script>alert('Error updating product: " . $conn->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>