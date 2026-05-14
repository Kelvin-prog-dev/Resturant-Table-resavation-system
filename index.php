<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Zest Restaurant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="zest-wrap">

        <!-- Hero -->
        <div class="zest-hero">
            <p class="zest-tagline">
                <i class="ti ti-flame"></i> Fine dining experience
            </p>
            <h1>Welcome to <span>Zest</span></h1>
            <p>A family-friendly restaurant offering cuisines from around the world, crafted with fresh, locally-sourced ingredients for a truly memorable dining experience.</p>
            <a href="reservation_form.php" class="zest-reserve-btn">Reserve a Table &rarr;</a>
        </div>

        <!-- Hours & Contact -->
        <div class="zest-grid">

            <div class="zest-card">
                <div class="zest-card-label">
                    <i class="ti ti-clock"></i> Hours
                </div>
                <div class="hours-row">
                    <span class="hours-day">Mon &ndash; Fri</span>
                    <span class="hours-time">11 AM &ndash; 10 PM</span>
                </div>
                <div class="hours-row">
                    <span class="hours-day">Saturday</span>
                    <span class="hours-time">12 PM &ndash; 11 PM</span>
                </div>
                <div class="hours-row">
                    <span class="hours-day">Sunday</span>
                    <span class="hours-time">12 PM &ndash; 9 PM</span>
                </div>
                <div>
                    <span class="hours-badge">Open today</span>
                </div>
            </div>

            <div class="zest-card">
                <div class="zest-card-label">
                    <i class="ti ti-map-pin"></i> Find us
                </div>
                <div class="contact-row">
                    <div class="contact-icon"><i class="ti ti-building-store"></i></div>
                    <div>
                        <div class="contact-label">Address</div>
                        <div class="contact-value">123 Main Street, Cityville, State 12345</div>
                    </div>
                </div>
                <div class="contact-row">
                    <div class="contact-icon"><i class="ti ti-phone"></i></div>
                    <div>
                        <div class="contact-label">Phone</div>
                        <div class="contact-value">+234 567 8901</div>
                    </div>
                </div>
                <div class="contact-row">
                    <div class="contact-icon"><i class="ti ti-mail"></i></div>
                    <div>
                        <div class="contact-label">Email</div>
                        <div class="contact-value">info@deliciousbites.com</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Features -->
        <div class="zest-full-card">
            <div class="zest-card-label">
                <i class="ti ti-star"></i> What we offer
            </div>
            <ul class="features-list">
                <li><span class="feat-dot"></span>Outdoor seating available</li>
                <li><span class="feat-dot"></span>Private dining rooms</li>
                <li><span class="feat-dot"></span>Full bar &amp; wine selection</li>
                <li><span class="feat-dot"></span>Vegetarian &amp; vegan options</li>
                <li><span class="feat-dot"></span>Takeout &amp; delivery</li>
                <li><span class="feat-dot"></span>Special occasion hosting</li>
            </ul>
        </div>

        <!-- Admin (discreet) -->
        <div class="zest-footer">
            <a href="admin/login.php">Admin</a>
        </div>

    </div>
</body>
</html>