<?php
// admin/dashboard.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

require_once '../config.php';
$conn = getDBConnection();

$flash = '';
$flashType = 'success';

// ── Handle cancellation ─────────────────────────────────────────
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $rid  = (int)$_GET['cancel'];
    $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
    $stmt->bind_param("i", $rid);
    $stmt->execute();
    $stmt->close();
    $flash = "Reservation #" . str_pad($rid, 6, '0', STR_PAD_LEFT) . " has been cancelled.";
}

// ── Handle confirmation ─────────────────────────────────────────
if (isset($_GET['confirm']) && is_numeric($_GET['confirm'])) {
    $rid  = (int)$_GET['confirm'];
    $stmt = $conn->prepare("UPDATE reservations SET status = 'confirmed' WHERE reservation_id = ?");
    $stmt->bind_param("i", $rid);
    $stmt->execute();
    $stmt->close();
    $flash = "Reservation #" . str_pad($rid, 6, '0', STR_PAD_LEFT) . " has been confirmed.";
}

// ── Handle deletion ─────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $rid  = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ?");
    $stmt->bind_param("i", $rid);
    $stmt->execute();
    $stmt->close();
    $flash = "Reservation #" . str_pad($rid, 6, '0', STR_PAD_LEFT) . " has been deleted.";
}

// ── Fetch stats ─────────────────────────────────────────────────
$statsQuery = "SELECT
    COUNT(*) AS total,
    SUM(status = 'confirmed') AS confirmed,
    SUM(status = 'pending')   AS pending,
    SUM(status = 'cancelled') AS cancelled
FROM reservations";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult ? $statsResult->fetch_assoc() : ['total'=>0,'confirmed'=>0,'pending'=>0,'cancelled'=>0];

// ── Fetch all reservations ──────────────────────────────────────
$sql = "
    SELECT r.reservation_id, r.reservation_date, r.reservation_time,
           r.guests, r.special_requests, r.status, r.table_number,
           c.customer_name, c.email, c.phone_number
    FROM reservations r
    JOIN customers c ON r.customer_id = c.customer_id
    ORDER BY r.reservation_date DESC, r.reservation_time DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Zest Restaurant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="admin-wrap">

    <!-- Hero header -->
    <div class="admin-hero">
        <div class="admin-hero-left">
            <span class="admin-tagline"><i class="ti ti-shield-lock"></i> Admin Portal</span>
            <h1>Zest <span>Dashboard</span></h1>
        </div>
        <a href="?logout=1" class="admin-logout"
           onclick="return confirm('Log out of the admin panel?')">
            <i class="ti ti-logout"></i> Log out
        </a>
    </div>

    <!-- Flash message -->
    <?php if ($flash): ?>
    <div class="flash flash-<?= $flashType ?>">
        <i class="ti ti-circle-check"></i>
        <?= htmlspecialchars($flash) ?>
    </div>
    <?php endif; ?>

    <!-- Stats row -->
    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-label">Total reservations</div>
            <div class="stat-value gold"><?= (int)$stats['total'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Confirmed</div>
            <div class="stat-value green"><?= (int)$stats['confirmed'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value gold"><?= (int)$stats['pending'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Cancelled</div>
            <div class="stat-value red"><?= (int)$stats['cancelled'] ?></div>
        </div>
    </div>

    <!-- Reservations table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title">
                <i class="ti ti-calendar-event"></i> Reservations
            </span>
            <span style="font-size:12px;color:#bbb;"><?= (int)$stats['total'] ?> total</span>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Customer</th>
                    <th>Date &amp; Time</th>
                    <th>Guests</th>
                    <th>Table</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td style="color:#bbb;font-size:12px;">
                        #<?= str_pad($row['reservation_id'], 6, '0', STR_PAD_LEFT) ?>
                    </td>
                    <td>
                        <div class="td-name"><?= htmlspecialchars($row['customer_name']) ?></div>
                        <div class="td-sub"><?= htmlspecialchars($row['email']) ?></div>
                        <div class="td-sub"><?= htmlspecialchars($row['phone_number']) ?></div>
                    </td>
                    <td>
                        <div class="td-name">
                            <?= date('d M Y', strtotime($row['reservation_date'])) ?>
                        </div>
                        <div class="td-sub"><?= htmlspecialchars($row['reservation_time']) ?></div>
                    </td>
                    <td><?= (int)$row['guests'] ?></td>
                    <td>
                        <?= $row['table_number'] ? '#' . (int)$row['table_number'] : '<span style="color:#ddd">—</span>' ?>
                    </td>
                    <td>
                        <?php
                        $s = $row['status'];
                        $badgeClass = match($s) {
                            'confirmed' => 'badge-confirmed',
                            'pending'   => 'badge-pending',
                            'cancelled' => 'badge-cancelled',
                            'active'    => 'badge-active',
                            default     => 'badge-pending',
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= ucfirst(htmlspecialchars($s)) ?></span>
                    </td>
                    <td style="max-width:140px;">
                        <?php if (!empty($row['special_requests'])): ?>
                            <span style="font-size:12px;color:#888;line-height:1.4;">
                                <?= htmlspecialchars(mb_strimwidth($row['special_requests'], 0, 60, '…')) ?>
                            </span>
                        <?php else: ?>
                            <span style="color:#ddd;font-size:12px;">None</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <?php if ($s === 'pending'): ?>
                                <a href="?confirm=<?= $row['reservation_id'] ?>" class="act-btn act-confirm"
                                   onclick="return confirm('Confirm this reservation?')">
                                    <i class="ti ti-check"></i> Confirm
                                </a>
                                <a href="?cancel=<?= $row['reservation_id'] ?>" class="act-btn act-cancel"
                                   onclick="return confirm('Cancel this reservation?')">
                                    <i class="ti ti-x"></i> Cancel
                                </a>
                            <?php elseif ($s === 'confirmed' || $s === 'active'): ?>
                                <a href="?cancel=<?= $row['reservation_id'] ?>" class="act-btn act-cancel"
                                   onclick="return confirm('Cancel this reservation?')">
                                    <i class="ti ti-x"></i> Cancel
                                </a>
                            <?php elseif ($s === 'cancelled'): ?>
                                <a href="?delete=<?= $row['reservation_id'] ?>" class="act-btn act-delete"
                                   onclick="return confirm('Permanently delete this reservation?')">
                                    <i class="ti ti-trash"></i> Delete
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php else: ?>
        <div class="admin-empty">
            <i class="ti ti-calendar-off"></i>
            No reservations found.
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div style="text-align:center;font-size:11px;color:#ccc;padding-bottom:1.5rem;">
        Zest Restaurant Admin &mdash; <a href="../index.php" style="color:#ccc;text-decoration:none;">View site</a>
    </div>

</div>

<?php
if ($result) $result->close();
$conn->close();
?>
</body>
</html>