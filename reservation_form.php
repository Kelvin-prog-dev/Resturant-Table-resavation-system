<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Reservation - Zest Restaurant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #f5f0e8;
            color: #1a1208;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .res-wrap {
            max-width: 820px;
            margin: 0 auto;
        }

        /* Hero */
        .res-hero {
            background: #1a1208;
            border-radius: 16px;
            padding: 2rem 2.5rem;
            margin-bottom: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .res-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 80% 50%, #c8860a1a 0%, transparent 60%);
            pointer-events: none;
        }

        .res-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #a89880;
            text-decoration: none;
            margin-bottom: 1rem;
            transition: color 0.2s;
        }

        .res-back:hover { color: #c8860a; }

        .res-tagline {
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #c8860a;
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .res-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            font-weight: 700;
            color: #f5e6c8;
            line-height: 1.15;
        }

        .res-hero h1 span { color: #c8860a; }

        .res-hero p {
            font-size: 13px;
            color: #a89880;
            margin-top: 0.5rem;
            font-weight: 300;
        }

        /* Card */
        .res-card {
            background: #ffffff;
            border: 0.5px solid rgba(0,0,0,0.1);
            border-radius: 16px;
            padding: 2rem;
        }

        /* Form grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .res-hero { padding: 1.75rem 1.5rem; }
            .res-hero h1 { font-size: 30px; }
            .res-card { padding: 1.5rem; }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full { grid-column: 1 / -1; }

        .form-group label {
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #c8860a;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: #1a1208;
            background: #f5f0e8;
            border: 0.5px solid rgba(0,0,0,0.12);
            border-radius: 8px;
            padding: 10px 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #c8860a;
            box-shadow: 0 0 0 3px #c8860a18;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 90px;
        }

        /* Divider */
        .form-divider {
            height: 0.5px;
            background: rgba(0,0,0,0.08);
            grid-column: 1 / -1;
            margin: 0.5rem 0;
        }

        /* Section titles */
        .section-title {
            font-size: 10px;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #aaa;
            font-weight: 500;
            grid-column: 1 / -1;
            margin-bottom: -0.25rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Guest pills */
        .guest-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            grid-column: 1 / -1;
        }

        .guest-pill {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 0.5px solid rgba(0,0,0,0.12);
            background: #f5f0e8;
            font-size: 13px;
            font-weight: 500;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
            font-family: 'DM Sans', sans-serif;
            user-select: none;
        }

        .guest-pill:hover {
            border-color: #c8860a;
            color: #c8860a;
        }

        .guest-pill.active {
            background: #c8860a;
            border-color: #c8860a;
            color: #1a1208;
        }

        /* Submit */
        .res-submit {
            display: block;
            width: 100%;
            background: #1a1208;
            color: #f5e6c8;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: background 0.2s, color 0.2s;
        }

        .res-submit:hover {
            background: #c8860a;
            color: #1a1208;
        }

        .optional-note {
            font-size: 10px;
            color: #aaa;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 400;
            margin-left: 4px;
        }
    </style>
</head>
<body>
    <div class="res-wrap">

        <!-- Hero -->
        <div class="res-hero">
            <a href="index.php" class="res-back">
                <i class="ti ti-arrow-left"></i> Back to Zest
            </a>
            <p class="res-tagline">
                <i class="ti ti-calendar"></i> Reservations
            </p>
            <h1>Reserve your <span>Table</span></h1>
            <p>We'll confirm your booking within 24 hours.</p>
        </div>

        <!-- Form card -->
        <div class="res-card">
            <form action="process_reservation.php" method="POST">
                <div class="form-grid">

                    <!-- Your details -->
                    <div class="section-title">
                        <i class="ti ti-user" style="font-size:12px"></i> Your details
                    </div>

                    <div class="form-group">
                        <label for="name">Full name</label>
                        <input type="text" id="name" name="name" placeholder="Jane Smith" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="jane@example.com" required>
                    </div>

                    <div class="form-group full">
                        <label for="phone">Phone number</label>
                        <input type="tel" id="phone" name="phone" placeholder="(555) 000-0000" required>
                    </div>

                    <div class="form-divider"></div>

                    <!-- Date & time -->
                    <div class="section-title">
                        <i class="ti ti-clock" style="font-size:12px"></i> Date &amp; time
                    </div>

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="time">Time</label>
                        <select id="time" name="time" required>
                            <option value="">Select time</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="11:30">11:30 AM</option>
                            <option value="12:00">12:00 PM</option>
                            <option value="12:30">12:30 PM</option>
                            <option value="13:00">1:00 PM</option>
                            <option value="13:30">1:30 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="14:30">2:30 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="15:30">3:30 PM</option>
                            <option value="16:00">4:00 PM</option>
                            <option value="16:30">4:30 PM</option>
                            <option value="17:00">5:00 PM</option>
                            <option value="17:30">5:30 PM</option>
                            <option value="18:00">6:00 PM</option>
                            <option value="18:30">6:30 PM</option>
                            <option value="19:00">7:00 PM</option>
                            <option value="19:30">7:30 PM</option>
                            <option value="20:00">8:00 PM</option>
                            <option value="20:30">8:30 PM</option>
                            <option value="21:00">9:00 PM</option>
                            <option value="21:30">9:30 PM</option>
                            <option value="22:00">10:00 PM</option>
                        </select>
                    </div>

                    <div class="form-divider"></div>

                    <!-- Guests -->
                    <div class="section-title">
                        <i class="ti ti-users" style="font-size:12px"></i> Guests
                    </div>

                    <div class="guest-pills" id="guestPills">
                        <input type="hidden" name="guests" id="guestsInput" value="">
                        <div class="guest-pill" data-val="1">1</div>
                        <div class="guest-pill" data-val="2">2</div>
                        <div class="guest-pill" data-val="3">3</div>
                        <div class="guest-pill" data-val="4">4</div>
                        <div class="guest-pill" data-val="5">5</div>
                        <div class="guest-pill" data-val="6">6</div>
                        <div class="guest-pill" data-val="7">7</div>
                        <div class="guest-pill" data-val="8">8</div>
                        <div class="guest-pill" data-val="9">9</div>
                        <div class="guest-pill" data-val="10">10</div>
                    </div>

                    <div class="form-divider"></div>

                    <!-- Special requests -->
                    <div class="form-group full">
                        <label for="message">
                            Special requests <span class="optional-note">(optional)</span>
                        </label>
                        <textarea id="message" name="message" placeholder="Allergies, accessibility needs, celebrations…"></textarea>
                    </div>

                </div>

                <button type="submit" class="res-submit">Confirm Reservation &rarr;</button>
            </form>
        </div>

    </div>

    <script>
        document.querySelectorAll('.guest-pill').forEach(function(pill) {
            pill.addEventListener('click', function() {
                document.querySelectorAll('.guest-pill').forEach(function(p) {
                    p.classList.remove('active');
                });
                pill.classList.add('active');
                document.getElementById('guestsInput').value = pill.dataset.val;
            });
        });
    </script>
</body>
</html>