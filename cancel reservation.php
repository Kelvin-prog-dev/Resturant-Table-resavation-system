<?php
// cancel_reservation.php
// Called by payment.php via fetch() when the countdown timer expires.
// Marks the pending reservation as cancelled so the table is freed.
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = (int)($_POST['reservation_id'] ?? 0);
    if ($reservation_id > 0) {
        $conn = getDBConnection();
        $stmt = $conn->prepare(
            "UPDATE reservations SET status = 'cancelled'
             WHERE reservation_id = ? AND status = 'pending'"
        );
        if ($stmt) {
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $stmt->close();
        }
        $conn->close();
    }
    unset($_SESSION['zest_reservation']);
}

http_response_code(204); // No content — JS ignores the response
exit();
?>