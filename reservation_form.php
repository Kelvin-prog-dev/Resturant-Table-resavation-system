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
    <link rel="stylesheet" href="styles.css">
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