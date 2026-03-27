<?php
// process_reservation.php - Restaurant Table Reservation System
require_once 'config.php';

// Get database connection
$conn = getDBConnection();

/**
 * Check if tables are available for the requested date, time, and guest count
 * 
 * @param mysqli $conn - Database connection
 * @param string $date - Reservation date (YYYY-MM-DD)
 * @param string $time - Reservation time (HH:MM format)
 * @param int $guests - Number of guests
 * @return array - Array with 'available' (bool) and optional 'available_table_id' (int)
 */
function checkTableAvailability($conn, $date, $time, $guests) {
    // Query to find tables that:
    // 1. Have capacity for the requested number of guests
    // 2. Are NOT already booked for the requested date and time (with 2-hour window)
    
    $query = "
        SELECT t.table_number, t.capacity 
        FROM tables t 
        WHERE t.capacity >= ? 
        AND t.table_number NOT IN (
            SELECT DISTINCT r.table_number
            FROM reservations r 
            WHERE r.reservation_date = ? 
            AND TIME_FORMAT(r.reservation_time, '%H:%i') = ? 
            AND r.status IN ('confirmed', 'active')
            AND r.table_number IS NOT NULL
        )
        LIMIT 1
    ";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        // If tables don't exist yet, return available (for backward compatibility)
        return array('available' => true, 'available_table_number' => null);
    }
    
    $stmt->bind_param("iss", $guests, $date, $time);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        return array(
            'available' => true,
            'available_table_number' => $row['table_number']
        );
    } else {
        $stmt->close();
        return array('available' => false);
    }
}

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
        // Check for table availability before making reservation
        $table_status = checkTableAvailability($conn, $date, $time, $guests);

        if (!$table_status['available']) {
            // No tables available - notify user
            echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>No Tables Available - Zest Restaurant</title>
    <link rel='stylesheet' href='styles.css'>
    <style>
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            margin: 50px auto;
            max-width: 600px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
        .error-message h3 {
            margin-top: 0;
            color: #721c24;
        }
        .back-link {
            margin-top: 20px;
        }
        .back-link a {
            color: #721c24;
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
        <div class='error-message'>
            <h3>❌ No Tables Available</h3>
            <p>Sorry, <strong>" . htmlspecialchars($name) . "</strong>! Unfortunately, all tables are fully booked for:</p>
            <p>
                <strong>Date:</strong> " . htmlspecialchars($date) . "<br>
                <strong>Time:</strong> " . htmlspecialchars($time) . "<br>
                <strong>Guests:</strong> " . htmlspecialchars($guests) . "
            </p>
            <p>Please try a different date, time, or number of guests.</p>
            <div class='back-link'>
                <a href='reservation_form.php'>← Try Another Time</a> |
                <a href='resturant info.php'>← Back to Restaurant Info</a>
            </div>
        </div>
    </div>
</body>
</html>";
        } else {
            // Ensure we have an actual table number from availability check
            $assigned_table_number = $table_status['available_table_number'] ?? null;

            if (empty($assigned_table_number)) {
                echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px auto; max-width: 600px;'>";
                echo "<h3>❌ No Table Assigned</h3>";
                echo "<p>Sorry, we could not find an available table at this time. Please try another slot.</p>";
                echo "<p><a href='reservation_form.php'>← Back to Reservation Form</a></p>";
                echo "</div>";
                exit;
            }

            // Check if customer already exists by email
            $stmt_check_customer = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
            if ($stmt_check_customer === false) {
                die("Prepare failed for customer check: " . $conn->error);
            }
            $stmt_check_customer->bind_param("s", $email);
            $stmt_check_customer->execute();
            $result_check = $stmt_check_customer->get_result();

            if ($result_check->num_rows > 0) {
                // Customer exists, get their ID
                $row = $result_check->fetch_assoc();
                $customer_id = $row['customer_id'];
                $stmt_check_customer->close();
            } else {
                // Customer doesn't exist, insert new one
                $stmt_check_customer->close();
                $stmt_customer = $conn->prepare("INSERT INTO customers (customer_name, email, phone_number) VALUES (?, ?, ?)");
                if ($stmt_customer === false) {
                    die("Prepare failed for customers: " . $conn->error);
                }
                $stmt_customer->bind_param("sss", $name, $email, $phone);
                if (!$stmt_customer->execute()) {
                    die("Execute failed for customers: " . $stmt_customer->error);
                }
                $customer_id = $conn->insert_id;
                $stmt_customer->close();
            }


            // Now, insert into reservations table (including assigned table number)
            $stmt_reservation = $conn->prepare("INSERT INTO reservations (customer_id, table_number, reservation_date, reservation_time, guests, special_requests, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmed')");

            if ($stmt_reservation === false) {
                die("Prepare failed for reservations: " . $conn->error);
            }

            // Bind parameters for reservations
            $stmt_reservation->bind_param("iissis", $customer_id, $assigned_table_number, $date, $time, $guests, $message);

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
        }
    }
} else {
    // If not a POST request, redirect to form
    header("Location: reservation_form.php");
    exit();
}

// Close database connection
$conn->close();
?>
