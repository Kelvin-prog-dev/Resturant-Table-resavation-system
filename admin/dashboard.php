<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo "Zest Restaurant"; ?></title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .dashboard {
            max-width: 1200px;
            margin: 20px auto;
        }
        .dashboard h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .logout-btn {
            float: right;
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .cancel-btn:hover {
            background-color: #c82333;
        }
        .delete-btn {
            background-color: #8b0000;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .delete-btn:hover {
            background-color: #a00000;
        }
        .status-confirmed {
            color: #28a745;
            font-weight: bold;
        }
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
        .no-reservations {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container dashboard">
        <?php
        session_start();

        // Check if admin is logged in
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header("Location: login.php");
            exit();
        }

        require_once '../config.php';
        $conn = getDBConnection();

        // Handle cancellation
        if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
            $reservation_id = (int)$_GET['cancel'];

            // Update reservation status to cancelled
            $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $stmt->close();

            echo '<div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">Reservation cancelled successfully!</div>';
        }

        // Handle deletion
        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $reservation_id = (int)$_GET['delete'];

            // Delete the reservation
            $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $stmt->close();

            echo '<div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">Reservation deleted successfully!</div>';
        }

        // Fetch all reservations with customer info
        $sql = "SELECT r.reservation_id, r.reservation_date, r.reservation_time, r.guests, r.special_requests, r.status,
                       c.customer_name, c.email, c.phone_number
                FROM reservations r
                JOIN customers c ON r.customer_id = c.customer_id
                ORDER BY r.reservation_date DESC, r.reservation_time DESC";

        $result = $conn->query($sql);
        ?>

        <h2>Admin Dashboard</h2>
        <a href="?logout=1" class="logout-btn">Logout</a>

        <?php if (isset($_GET['logout'])) {
            session_destroy();
            header("Location: login.php");
            exit();
        } ?>

        <h3>Reservations Management</h3>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Guests</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['reservation_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['email']); ?><br>
                                <?php echo htmlspecialchars($row['phone_number']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['reservation_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['reservation_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['guests']); ?></td>
                            <td>
                                <span class="status-<?php echo htmlspecialchars($row['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] !== 'cancelled'): ?>
                                    <a href="?cancel=<?php echo $row['reservation_id']; ?>" class="cancel-btn"
                                       onclick="return confirm('Are you sure you want to cancel this reservation?')">Cancel</a>
                                <?php else: ?>
                                    <a href="?delete=<?php echo $row['reservation_id']; ?>" class="delete-btn"
                                       onclick="return confirm('Are you sure you want to delete this cancelled reservation?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-reservations">
                <p>No reservations found.</p>
            </div>
        <?php endif; ?>

        <?php
        $result->close();
        $conn->close();
        ?>
    </div>
</body>
</html>