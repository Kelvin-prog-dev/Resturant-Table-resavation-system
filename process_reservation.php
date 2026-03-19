 <?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "Kelvin@254!"; // Default XAMPP password
$dbname = "restaurant_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $phone = htmlspecialchars($_POST['phone']);
            $date = htmlspecialchars($_POST['date']);
            $time = htmlspecialchars($_POST['time']);
            $guests = htmlspecialchars($_POST['guests']);
            $message = htmlspecialchars($_POST['message']);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO reservations (name, email, phone, reservation_date, reservation_time, guests, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $name, $email, $phone, $date, $time, $guests, $message);

    if ($stmt->execute()) {
            echo "<div style='background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; margin-top: 150px; align-items: center; justify-content: center; display: flex; flex-direction: column; width: 100%; margin-left: 100px; margin-right: 100px;'>";
            echo "<h3>Reservation Request Submitted!</h3>";
            echo "<p>Thank you, $name. We have received your reservation request for $guests guests on $date at $time.</p>";
            echo "<p>We will contact you at $phone or $email to confirm your reservation.</p>";
            echo "</div>";
        }
        ?>