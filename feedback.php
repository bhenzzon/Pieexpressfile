<?php
include 'db_connect.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $image = null;

    // Check if an image was uploaded
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "uploads/";
    
        // Create the uploads folder if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    
        $imageName = basename($_FILES["image"]["name"]);
        $imagePath = $uploadDir . $imageName; // Full image path
    
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            $image = $imagePath; // Store the correct image path
        } else {
            die("Error uploading image.");
        }
    }

    // Insert feedback into the database
    $sql = "INSERT INTO feedback (name, email, message, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $message, $image);

    if ($stmt->execute()) {
        echo "Feedback submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>