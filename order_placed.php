<?php
session_start();
$connection = new mysqli("localhost", "root", "", "ordering_system");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Assume the customer is identified by their contact number (or session data)
if (!isset($_SESSION['customer_contact'])) {
    die("<div class='back-button' >You need to log in first! <a href='javascript:history.back()'>Go back</a></div>");
}

$customer_contact = $_SESSION['customer_contact'];

// Fetch all orders related to this customer
$order_query = $connection->prepare("SELECT * FROM orders WHERE contact = ? ORDER BY id DESC");
$order_query->bind_param("s", $customer_contact);
$order_query->execute();
$order_result = $order_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
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
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            text-align: center;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 10%;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: rgba(1, 50, 32, 1);
            color: white;
        }
        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;
        }
        .pending { background-color: orange; }
        .completed { background-color: green; }
        .cancelled { background-color: red; }
        .preparing { background-color: #0f5298; color: white; padding: 5px; border-radius: 5px; }
        .delivered { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        .to, .ship { background-color: darkgreen; color: white; padding: 5px; border-radius: 5px; }
        .received { background-color: green; color: white; padding: 5px; border-radius: 5px; }
        a.button {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: white;
            background: rgba(1, 50, 32, 1);
            padding: 10px 15px;
            border-radius: 5px;
        }
        a.button:hover {
            background: #45a049;
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
    .back-button {
        background-color: #013220; /* Very dark green */
        color: white;
        padding: 10px 20px;
        font-size: 18px;
        text-decoration: none;
        border-radius: 5px;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s ease;
    }
    .back-button:hover {
        background-color: #024d2a; /* Slightly lighter dark green on hover */
    }

                 #footer {
                background-color: #013220;
                color: #fff;
                padding: 40px 20px;
                text-align: center;
                margin-top: 13%;
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
              .cancel-button {
                    display: inline-block;
                    margin-top: 15px;
                    text-decoration: none;
                    color: white;
                    background-color: #dc3545;
                    padding: 10px 15px;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-left: 10px;
                    margin-right:-70px;
                }

                .cancel-button:hover {
                    background-color: darkred;
                }
                .delivered-button {
                    display: inline-block;
                    margin-top: 15px;
                    text-decoration: none;
                    color: white;
                    background: green;
                    padding: 10px 15px;
                    border-radius: 5px;
                    cursor: pointer;
                    margin-left: 10px;
                    margin-right:-90px;
                }

                .delivered-button:hover {
                    background-color: darkgreen;
                }
                /* Custom Modal Background */
.custom-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* Modal Content */
.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
    width: 300px;
}

/* Modal Text */
.modal-content p {
    font-size: 20px;
    font-weight: bold;
    color: #013220;
    margin-bottom: 15px;
}

/* Modal Buttons */
.confirm-btn, .cancel-btn {
    padding: 10px 15px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

/* Confirm Button - Green */
.confirm-btn {
    background-color: green;
    color: white;
}

.confirm-btn:hover {
    background-color: darkgreen;
}

/* Cancel Button - Red */
.cancel-btn {
    background-color: red;
    color: white;
}

.cancel-btn:hover {
    background-color: darkred;
}
.message-box {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    background-color: #ffcccc; /* Light red */
    color: #900; /* Dark red */
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    position: fixed;
    top: 20%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
}
/* Modal Background */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* Modal Content */
.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    width: 300px;
}

/* Modal Text */
.modal-message {
    font-size: 18px;
    font-weight: bold;
    color: #900;
    margin-bottom: 15px;
}

/* Buttons */
.modal-buttons button {
    background: #900;
    color: white;
    border: none;
    padding: 8px 16px;
    margin: 5px;
    cursor: pointer;
    border-radius: 5px;
}

.modal-buttons button.cancel {
    background: gray;
}
#imagePreview {
    max-width: 80px;  /* Adjust image size */
    max-height: 80px;
    display: none;
    border-radius: 5px;
    margin: 10px auto;  /* Center inside the form */
    display: block;
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
            <a href="#">My Purchases</a>
            <a href="/project/project-folder/php/checkout.php" class="fas fa-shopping-cart"></a>
            
            
        </div>
    </nav>

    <div class="container">
    <h2>Your Orders</h2>

    <?php if ($order_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
            <?php while ($order = $order_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo $order['created_at']; ?></td>
                <td><span class="status <?php echo strtolower($order['order_status']); ?>">
                    <?php echo htmlspecialchars($order['order_status']); ?>
                </span></td>
                <td>
    <a href="your_orders.php?order_id=<?php echo $order['id']; ?>" class="button">View</a>
    <?php if ($order['order_status'] === 'Pending'): ?>
    <a class="cancel-button" data-order-id="<?php echo $order['id']; ?>">Cancel</a>
<?php endif; ?>
<?php if ($order['order_status'] === 'To Ship'): ?>
    <a class="delivered-button" data-order-id="<?php echo $order['id']; ?>">Order Received</a>
<?php endif; ?>
</td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>

    <a class="button" href="/project/menu.php#healthy-drinks">Back to Home</a>
    <a class="button" href="/project/project-folder/php/cs_order_history.php">Order History</a>
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
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".cancel-button").forEach(button => {
        button.addEventListener("click", function () {
            const orderId = this.getAttribute("data-order-id");

            // Create modal dynamically
            const modal = document.createElement("div");
            modal.classList.add("modal");

            const modalContent = document.createElement("div");
            modalContent.classList.add("modal-content");

            const message = document.createElement("p");
            message.classList.add("modal-message");
            message.innerText = "Are you sure you want to cancel this order?";

            const buttonContainer = document.createElement("div");
            buttonContainer.classList.add("modal-buttons");

            const confirmButton = document.createElement("button");
            confirmButton.innerText = "Yes, Cancel Order";
            confirmButton.addEventListener("click", function () {
                // Debugging: Check if orderId is correct
                console.log("Cancelling order with ID:", orderId);

                fetch("/project/project-folder/php/cancel_order.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "order_id=" + encodeURIComponent(orderId)  // Make sure ID is properly encoded
                })
                .then(response => response.text())
                .then(data => {
                    console.log("Response from server:", data); // Debugging: Show server response
                    document.body.removeChild(modal);
                    location.reload(); // Refresh page after order cancellation
                })
                .catch(error => console.error("Error:", error)); // Debugging: Log errors
            });

            const cancelButton = document.createElement("button");
            cancelButton.innerText = "No, Keep Order";
            cancelButton.classList.add("cancel");
            cancelButton.addEventListener("click", function () {
                document.body.removeChild(modal);
            });

            // Append elements
            buttonContainer.appendChild(confirmButton);
            buttonContainer.appendChild(cancelButton);
            modalContent.appendChild(message);
            modalContent.appendChild(buttonContainer);
            modal.appendChild(modalContent);
            document.body.appendChild(modal);

            // Show modal
            modal.style.display = "flex";
        });
    });
});
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delivered-button").forEach(button => {
        button.addEventListener("click", function () {
            const orderId = this.getAttribute("data-order-id");

            // Create the modal
            const modal = document.createElement("div");
            modal.classList.add("custom-modal");
            modal.innerHTML = `
                <div class="modal-content">
                    <p>Have you received your order?</p>
                    <button class="confirm-btn" data-order-id="${orderId}">Yes</button>
                    <button class="cancel-btn">No</button>
                </div>
            `;

            document.body.appendChild(modal);

            // Add event listeners
            modal.querySelector(".confirm-btn").addEventListener("click", function () {
                fetch("/project/project-folder/php/order_received.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "order_id=" + orderId
                })
                .then(response => response.text())
                .then(data => {
                    console.log("Server response:", data);
                    if (data.includes("Order marked as received")) {
                        const statusElement = button.closest("tr").querySelector(".status");
                        statusElement.textContent = "Received";
                        statusElement.classList.remove("to", "ship");
                        statusElement.classList.add("delivered");
                        button.remove();
                    } else {
                        alert(data);
                    }
                    modal.remove(); // Close modal
                });
            });

            modal.querySelector(".cancel-btn").addEventListener("click", function () {
                modal.remove(); // Close modal if "No" is clicked
            });
        });
    });
});

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

        fetch("/project/feedback.php", {
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

<?php
$order_query->close();
$connection->close();
?>