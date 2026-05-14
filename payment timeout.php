<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired — Zest Restaurant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="status-wrap">
    <div class="status-hero">
        <span class="status-icon">⏱️</span>
        <h1>Session <span>expired</span></h1>
        <p>Your 5-minute payment hold has timed out.</p>
    </div>
    <div class="status-card">
        <p style="font-size:14px;color:#555;margin-bottom:0.75rem;line-height:1.7;">
            Your table hold has been released so other guests can book it.
            This happens if the payment window is left open too long without completing payment.
        </p>
        <p style="font-size:13px;color:#aaa;margin-bottom:1.5rem;line-height:1.6;">
            Your details won't be lost — simply start a new reservation and
            complete payment within the 5-minute window.
        </p>
        <div class="status-actions">
            <a href="reservation_form.php" class="btn-primary">New reservation</a>
            <a href="index.php" class="btn-secondary">Home</a>
        </div>
    </div>
</div>
</body>
</html>