<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Delete the image file
    $query = "SELECT image_url FROM products WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    $stmt->fetch();
    $stmt->close();

    if ($image_url && file_exists($image_url)) {
        unlink($image_url);
    }

    // Delete product from database
    $sql = "DELETE FROM products WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Use HTTP_REFERER to go back to the same page
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'load_products.php';
        echo "<script>
                alert('Product deleted successfully!');
                window.location.href = '$redirect_url';
              </script>";
    } else {
        echo "Error deleting product: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>