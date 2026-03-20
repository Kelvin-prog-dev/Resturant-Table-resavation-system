<?php
// process_reservation.php - Restaurant Table Reservation System
// Database configuration
$servername = "localhost";
$username = "root";
$password = "Kelvin@254!"; 
$dbname = "resturant_system";

// database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and validate input
    $name = trim(htmlspecialchars($_POST['name'] ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $phone = trim(htmlspecialchars($_POST['phone'] ?? ''));
    $date = trim(htmlspecialchars($_POST['date'] ?? ''));
    $time = trim(htmlspecialchars($_POST['time'] ?? ''));
    $guests = trim(htmlspecialchars($_POST['guests'] ?? ''));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));

    // Basic validation
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }

    if (empty($date)) {
        $errors[] = "Reservation date is required";
    }

    if (empty($time)) {
        $errors[] = "Reservation time is required";
    }

    if (empty($guests) || !is_numeric($guests) || $guests < 1) {
        $errors[] = "Valid number of guests is required";
    }

    // If validation fails, display errors
    if (!empty($errors)) {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px auto; max-width: 600px;'>";
        echo "<h3>Validation Errors:</h3>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo "<p><a href='reservation_form.php'>← Back to Reservation Form</a></p>";
        echo "</div>";
    } else {
        // First, insert into customers table
        $stmt_customer = $conn->prepare("INSERT INTO customers (customer_name, email, phone_number) VALUES (?, ?, ?)");

        if ($stmt_customer === false) {
            die("Prepare failed for customers: " . $conn->error);
        }

        // Bind parameters for customers
        $stmt_customer->bind_param("sss", $name, $email, $phone);

        // Execute the customer insert
        if ($stmt_customer->execute()) {
            // Get the customer_id
            $customer_id = $conn->insert_id;

            // Now, insert into reservations table
            $stmt_reservation = $conn->prepare("INSERT INTO reservations (customer_id, reservation_date, reservation_time, guests, special_requests) VALUES (?, ?, ?, ?, ?)");

            if ($stmt_reservation === false) {
                die("Prepare failed for reservations: " . $conn->error);
            }

            // Bind parameters for reservations
            $stmt_reservation->bind_param("issis", $customer_id, $date, $time, $guests, $message);

            // Execute the reservation insert
            if ($stmt_reservation->execute()) {
                // Success message
                echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reservation Submitted - Zest Restaurant</title>
    <link rel='stylesheet' href='styles.css'>
    <style>
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 5px;
            margin: 50px auto;
            max-width: 600px;
            text-align: center;
            border: 1px solid #c3e6cb;
        }
        .success-message h3 {
            margin-top: 0;
            color: #155724;
        }
        .back-link {
            margin-top: 20px;
        }
        .back-link a {
            color: #155724;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='success-message'>
            <h3>✓ Reservation Request Submitted Successfully!</h3>
            <p>Thank you, <strong>" . htmlspecialchars($name) . "</strong>. We have received your reservation request.</p>
            <p><strong>Details:</strong></p>
            <ul style='text-align: left; display: inline-block;'>
                <li>Date: " . htmlspecialchars($date) . "</li>
                <li>Time: " . htmlspecialchars($time) . "</li>
                <li>Guests: " . htmlspecialchars($guests) . "</li>
                <li>Phone: " . htmlspecialchars($phone) . "</li>
                <li>Email: " . htmlspecialchars($email) . "</li>
            </ul>
            <p>We will contact you at <strong>" . htmlspecialchars($phone) . "</strong> or <strong>" . htmlspecialchars($email) . "</strong> to confirm your reservation within 24 hours.</p>
            <div class='back-link'>
                <a href='reservation_form.php'>← Make Another Reservation</a> |
                <a href='resturant info.php'>← Back to Restaurant Info</a>
            </div>
        </div>
    </div>
</body>
</html>";
            } else {
                // Error in reservation insert
                echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px auto; max-width: 600px;'>";
                echo "<h3>❌ Error Processing Reservation</h3>";
                echo "<p>Sorry, there was an error processing your reservation request. Please try again later.</p>";
                echo "<p>Error details: " . htmlspecialchars($stmt_reservation->error) . "</p>";
                echo "<p><a href='reservation_form.php'>← Back to Reservation Form</a></p>";
                echo "</div>";
            }

            // Close reservation statement
            $stmt_reservation->close();
        } else {
            // Error in customer insert
            echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px auto; max-width: 600px;'>";
            echo "<h3>❌ Error Processing Customer Information</h3>";
            echo "<p>Sorry, there was an error saving your information. Please try again later.</p>";
            echo "<p>Error details: " . htmlspecialchars($stmt_customer->error) . "</p>";
            echo "<p><a href='reservation_form.php'>← Back to Reservation Form</a></p>";
            echo "</div>";
        }

        // Close customer statement
        $stmt_customer->close();
    }
} else {
    // If not a POST request, redirect to form
    header("Location: reservation_form.php");
    exit();
}

// Close database connection
$conn->close();
?>
