<?php
// process_reservation.php - Restaurant Table Reservation System
session_start();
require_once 'config.php';

$conn = getDBConnection();

/**
 * Check table availability for a given date, time, and guest count.
 * Fetches table sizes from the DB and excludes tables already booked
 * within a 2-hour window of the requested time.
 *
 * @param mysqli $conn
 * @param string $date   YYYY-MM-DD
 * @param string $time   HH:MM  (24-hour)
 * @param int    $guests
 * @return array { available: bool, available_table_number: int|null }
 */
function checkTableAvailability($conn, $date, $time, $guests) {
    /*
     * Excludes any table that has a confirmed/active/pending booking
     * within 120 minutes either side of the requested time.
     * TIMESTAMPDIFF(MINUTE, time_a, time_b) is negative if time_a > time_b,
     * so we wrap in ABS() to catch both directions.
     */
    $query = "
        SELECT t.table_number, t.capacity
        FROM tables t
        WHERE t.capacity >= ?
          AND t.table_number NOT IN (
              SELECT DISTINCT r.table_number
              FROM reservations r
              WHERE r.reservation_date = ?
                AND r.status IN ('confirmed', 'active', 'pending')
                AND r.table_number IS NOT NULL
                AND ABS(TIMESTAMPDIFF(MINUTE, r.reservation_time, ?)) < 120
          )
        ORDER BY t.capacity ASC
        LIMIT 1
    ";

    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        error_log("checkTableAvailability prepare failed: " . $conn->error);
        return ['available' => true, 'available_table_number' => null];
    }

    $stmt->bind_param("iss", $guests, $date, $time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        return [
            'available'              => true,
            'available_table_number' => (int) $row['table_number'],
        ];
    }

    $stmt->close();
    return ['available' => false];
}

/* ── Helper: render a full Zest-styled status page ── */
function renderStatusPage(string $title, string $heroTitle, string $heroSub, string $bodyHtml): void {
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} — Zest Restaurant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="status-wrap">
    <div class="status-hero">
        <h1>{$heroTitle}</h1>
        <p>{$heroSub}</p>
    </div>
    <div class="status-card">
        {$bodyHtml}
    </div>
</div>
</body>
</html>
HTML;
}

/* ════════════════════════════════════════════
   Main request handler
   ════════════════════════════════════════════ */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: reservation_form.php");
    exit();
}

// ── 1. Sanitise inputs ──────────────────────────────────────────
$name    = trim(htmlspecialchars($_POST['name']    ?? ''));
$email   = trim(htmlspecialchars($_POST['email']   ?? ''));
$phone   = trim(htmlspecialchars($_POST['phone']   ?? ''));
$date    = trim(htmlspecialchars($_POST['date']    ?? ''));
$time    = trim(htmlspecialchars($_POST['time']    ?? ''));
$guests  = trim(htmlspecialchars($_POST['guests']  ?? ''));
$message = trim(htmlspecialchars($_POST['message'] ?? ''));

// ── 2. Validate ─────────────────────────────────────────────────
$errors = [];

if (empty($name))
    $errors[] = "Full name is required.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = "A valid email address is required.";
if (empty($phone))
    $errors[] = "Phone number is required.";
if (empty($date))
    $errors[] = "Reservation date is required.";
elseif (strtotime($date) < strtotime('today'))
    $errors[] = "Reservation date cannot be in the past.";
if (empty($time))
    $errors[] = "Reservation time is required.";
if (empty($guests) || !is_numeric($guests) || (int)$guests < 1)
    $errors[] = "Please select a valid number of guests.";

if (!empty($errors)) {
    $errorList = '<ul style="margin:0 0 1.25rem 1rem;">';
    foreach ($errors as $e)
        $errorList .= '<li style="margin-bottom:6px;font-size:14px;color:#555;">' . $e . '</li>';
    $errorList .= '</ul>';

    $body = $errorList . '
        <div class="status-actions">
            <a href="reservation_form.php" class="btn-primary">← Fix my details</a>
            <a href="index.php" class="btn-secondary">Home</a>
        </div>';
    renderStatusPage('Validation Error', 'Something\'s <span>missing</span>', 'Please fix the issues below and try again.', $body);
    $conn->close();
    exit();
}

$guests = (int) $guests;

// ── 3. Check table availability ─────────────────────────────────
$table_status = checkTableAvailability($conn, $date, $time, $guests);

if (!$table_status['available']) {
    $formattedDate = date('D, d M Y', strtotime($date));
    $body = "
        <div class='detail-row'><span class='detail-label'>Name</span><span class='detail-value'>" . htmlspecialchars($name) . "</span></div>
        <div class='detail-row'><span class='detail-label'>Date</span><span class='detail-value'>{$formattedDate}</span></div>
        <div class='detail-row'><span class='detail-label'>Time</span><span class='detail-value'>" . htmlspecialchars($time) . "</span></div>
        <div class='detail-row' style='margin-bottom:1.25rem'><span class='detail-label'>Guests</span><span class='detail-value'>{$guests}</span></div>
        <p style='font-size:13px;color:#888;margin-bottom:1.25rem;line-height:1.6;'>
            All tables with capacity for <strong>{$guests}</strong> guest(s) are fully booked during that time slot.
            Please try a different date or time.
        </p>
        <div class='status-actions'>
            <a href='reservation_form.php' class='btn-primary'>Try another slot</a>
            <a href='index.php' class='btn-secondary'>Home</a>
        </div>";
    renderStatusPage('No Tables Available', 'No tables <span>available</span>', 'All tables are fully booked for your selected slot.', $body);
    $conn->close();
    exit();
}

$assigned_table_number = $table_status['available_table_number'];

// ── 4. Upsert customer ──────────────────────────────────────────
$stmt_check = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
if ($stmt_check === false) die("DB error: " . $conn->error);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $customer_id = $result_check->fetch_assoc()['customer_id'];
    $stmt_check->close();
} else {
    $stmt_check->close();
    $stmt_cust = $conn->prepare("INSERT INTO customers (customer_name, email, phone_number) VALUES (?, ?, ?)");
    if ($stmt_cust === false) die("DB error: " . $conn->error);
    $stmt_cust->bind_param("sss", $name, $email, $phone);
    if (!$stmt_cust->execute()) die("DB error: " . $stmt_cust->error);
    $customer_id = $conn->insert_id;
    $stmt_cust->close();
}

// ── 5. Insert reservation with status = 'pending' ───────────────
//      Status is updated to 'confirmed' once payment succeeds.
$stmt_res = $conn->prepare(
    "INSERT INTO reservations
        (customer_id, table_number, reservation_date, reservation_time, guests, special_requests, status)
     VALUES (?, ?, ?, ?, ?, ?, 'pending')"
);
if ($stmt_res === false) die("DB error: " . $conn->error);
$stmt_res->bind_param("iissis", $customer_id, $assigned_table_number, $date, $time, $guests, $message);

if (!$stmt_res->execute()) {
    $errMsg = htmlspecialchars($stmt_res->error);
    $body = "
        <p style='font-size:14px;color:#555;margin-bottom:1.25rem;'>
            There was a database error while saving your reservation. Please try again.
        </p>
        <p style='font-size:12px;color:#aaa;margin-bottom:1.25rem;'>Details: {$errMsg}</p>
        <div class='status-actions'>
            <a href='reservation_form.php' class='btn-primary'>Try again</a>
            <a href='index.php' class='btn-secondary'>Home</a>
        </div>";
    renderStatusPage('Error', 'Something went <span>wrong</span>', 'We could not save your reservation.', $body);
    $stmt_res->close();
    $conn->close();
    exit();
}

$reservation_id = $conn->insert_id;
$stmt_res->close();
$conn->close();

// ── 6. Pass reservation details to payment page via session ─────
$_SESSION['zest_reservation'] = [
    'reservation_id'   => $reservation_id,
    'customer_id'      => $customer_id,
    'name'             => $name,
    'email'            => $email,
    'phone'            => $phone,
    'date'             => $date,
    'time'             => $time,
    'guests'           => $guests,
    'table_number'     => $assigned_table_number,
    'special_requests' => $message,
    'initiated_at'     => time(),   // payment.php uses this for the 5-min countdown
];

// ── 7. Redirect to payment page ─────────────────────────────────
header("Location: payment.php");
exit();
?>