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
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-image: url("C:/xampp/htdocs/Resturant Table Resavation System/images/zest.jpg");
            background-size: cover;
            background-position: center;
            color: #1a1208;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .zest-wrap {
            max-width: 820px;
            margin: 0 auto;
        }

        /* Hero */
        .zest-hero {
            position: relative;
            background: #1a1208;
            border-radius: 16px;
            padding: 3rem 2.5rem 2.5rem;
            margin-bottom: 1.25rem;
            overflow: hidden;
        }

        .zest-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 80% 20%, #c8860a22 0%, transparent 60%),
                        radial-gradient(ellipse at 10% 80%, #8b4a0a18 0%, transparent 50%);
            pointer-events: none;
        }

        .zest-tagline {
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #c8860a;
            font-weight: 500;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .zest-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 700;
            color: #f5e6c8;
            margin-bottom: 1rem;
            line-height: 1.1;
            letter-spacing: -0.01em;
        }

        .zest-hero h1 span {
            color: #c8860a;
        }

        .zest-hero p {
            font-size: 15px;
            color: #a89880;
            max-width: 480px;
            line-height: 1.7;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .zest-reserve-btn {
            display: inline-block;
            background: #c8860a;
            color: #1a1208;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 12px 28px;
            border-radius: 2px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .zest-reserve-btn:hover {
            background: #dfa020;
        }

        /* Grid */
        .zest-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 600px) {
            .zest-grid { grid-template-columns: 1fr; }
            .zest-hero h1 { font-size: 36px; }
            .zest-hero { padding: 2rem 1.5rem; }
            .features-list { grid-template-columns: 1fr; }
        }

        /* Cards */
        .zest-card, .zest-full-card {
            background:#1a1208;
            border: 0.5px solid rgba(0,0,0,0.1);
            border-radius: 16px;
            padding: 1.5rem;
        }

        .zest-full-card {
            margin-bottom: 1rem;
        }

        .zest-card-label {
            font-size: 10px;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #c8860a;
            font-weight: 500;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Hours */
        .hours-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 0.5px solid rgba(0,0,0,0.08);
            font-size: 13px;
        }

        .hours-row:last-of-type { border-bottom: none; }

        .hours-day { color: #888; font-weight: 400; }
        .hours-time { font-weight: 500; color: #1a1208; }

        .hours-badge {
            display: inline-block;
            background: #c8860a18;
            color: #c8860a;
            font-size: 10px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 500;
            letter-spacing: 0.05em;
            margin-top: 12px;
        }

        /* Contact */
        .contact-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 0.5px solid rgba(0,0,0,0.08);
            font-size: 13px;
            line-height: 1.5;
        }

        .contact-row:last-child { border-bottom: none; }

        .contact-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f5f0e8;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .contact-icon i { font-size: 15px; color: #c8860a; }

        .contact-label { font-size: 11px; color: #aaa; margin-bottom: 2px; }
        .contact-value { color: #1a1208; font-weight: 500; }

        /* Features */
        .features-list {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .features-list li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #555;
        }

        .feat-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #c8860a;
            flex-shrink: 0;
        }

        /* Footer */
        .zest-footer {
            text-align: center;
            padding: 0.5rem 0 0.25rem;
        }

        .zest-footer a {
            color: #bbb;
            text-decoration: none;
            font-size: 11px;
            opacity: 0.6;
        }

        .zest-footer a:hover { opacity: 1; }
    </style>
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
                        <div class="contact-value">(555) 123-4567</div>
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