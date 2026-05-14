<?php
// payment.php - Zest Restaurant Payment Module
session_start();
require_once 'config.php';

// ── Guard: must arrive via process_reservation.php ──────────────
if (empty($_SESSION['zest_reservation'])) {
    header("Location: reservation_form.php");
    exit();
}

$res  = $_SESSION['zest_reservation'];
$conn = getDBConnection();

// ── Check whether the 5-minute window has already expired ───────
$PAYMENT_TIMEOUT = 300; // seconds (5 minutes)
$elapsed         = time() - (int)($res['initiated_at'] ?? 0);
$remaining       = max(0, $PAYMENT_TIMEOUT - $elapsed);

if ($remaining === 0) {
    $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
    if ($stmt) { $stmt->bind_param("i", $res['reservation_id']); $stmt->execute(); $stmt->close(); }
    unset($_SESSION['zest_reservation']);
    $conn->close();
    header("Location: payment_timeout.php");
    exit();
}

// ── Handle payment form POST ─────────────────────────────────────
$paymentSuccess = false;
$paymentError   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $elapsedOnSubmit = time() - (int)($res['initiated_at'] ?? 0);
    if ($elapsedOnSubmit > $PAYMENT_TIMEOUT) {
        $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
        if ($stmt) { $stmt->bind_param("i", $res['reservation_id']); $stmt->execute(); $stmt->close(); }
        unset($_SESSION['zest_reservation']);
        $conn->close();
        header("Location: payment_timeout.php");
        exit();
    }

    $payMethod = trim($_POST['pay_method'] ?? 'card');
    $payErrors = [];

    if ($payMethod === 'card') {
        $cardName   = trim($_POST['card_name']   ?? '');
        $cardNumber = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
        $cardExpiry = trim($_POST['card_expiry'] ?? '');
        $cardCvv    = trim($_POST['card_cvv']    ?? '');
        if (empty($cardName))                                           $payErrors[] = "Cardholder name is required.";
        if (!preg_match('/^\d{13,19}$/', $cardNumber))                  $payErrors[] = "Enter a valid card number (13–19 digits).";
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $cardExpiry))    $payErrors[] = "Enter expiry as MM/YY.";
        if (!preg_match('/^\d{3,4}$/', $cardCvv))                       $payErrors[] = "Enter a valid CVV (3 or 4 digits).";
    }

    if ($payMethod === 'mpesa') {
        $mpesaPhone = preg_replace('/\s+/', '', $_POST['mpesa_phone'] ?? '');
        if (!preg_match('/^\+?\d{9,15}$/', $mpesaPhone))                $payErrors[] = "Enter a valid M-Pesa phone number.";
    }

    if (!empty($payErrors)) {
        $paymentError = implode(' ', $payErrors);
    } else {
        /*
         * ── Payment gateway integration point ────────────────────
         * Replace $chargeSuccess below with a real gateway call:
         *   Stripe:       \Stripe\Charge::create([...])
         *   Flutterwave:  flw_charge($cardNumber, $amount, ...)
         *   M-Pesa STK:   mpesa_stk_push($mpesaPhone, $amount, ...)
         */
        $chargeSuccess = true; // TODO: replace with real gateway result

        if ($chargeSuccess) {
            $stmt = $conn->prepare("UPDATE reservations SET status = 'confirmed' WHERE reservation_id = ?");
            if ($stmt) { $stmt->bind_param("i", $res['reservation_id']); $stmt->execute(); $stmt->close(); }
            $paymentSuccess = true;
            unset($_SESSION['zest_reservation']);
        } else {
            $paymentError = "Payment was declined. Please check your details and try again.";
        }
    }
}

$conn->close();

$formattedDate = date('D, d M Y', strtotime($res['date']));
$depositAmount = 500; // KES
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $paymentSuccess ? 'Booking Confirmed' : 'Complete Payment' ?> — Zest Restaurant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php if ($paymentSuccess): ?>
<!-- ════════ SUCCESS ════════ -->
<div class="status-wrap">
    <div class="status-hero">
        <span class="status-icon">🎉</span>
        <h1>You're all <span>set!</span></h1>
        <p>Payment confirmed &mdash; your table is reserved.</p>
    </div>
    <div class="status-card">
        <div class="detail-row"><span class="detail-label">Name</span><span class="detail-value"><?= htmlspecialchars($res['name']) ?></span></div>
        <div class="detail-row"><span class="detail-label">Date</span><span class="detail-value"><?= $formattedDate ?></span></div>
        <div class="detail-row"><span class="detail-label">Time</span><span class="detail-value"><?= htmlspecialchars($res['time']) ?></span></div>
        <div class="detail-row"><span class="detail-label">Guests</span><span class="detail-value"><?= (int)$res['guests'] ?></span></div>
        <div class="detail-row"><span class="detail-label">Table</span><span class="detail-value">#<?= (int)$res['table_number'] ?></span></div>
        <div class="detail-row"><span class="detail-label">Reference</span><span class="detail-value">#<?= str_pad($res['reservation_id'], 6, '0', STR_PAD_LEFT) ?></span></div>
        <p style="font-size:13px;color:#888;margin:1.25rem 0;line-height:1.6;">
            A confirmation will be sent to <strong><?= htmlspecialchars($res['email']) ?></strong>.
            We look forward to welcoming you!
        </p>
        <div class="status-actions">
            <a href="index.php" class="btn-primary">Back to Zest</a>
            <a href="reservation_form.php" class="btn-secondary">New reservation</a>
        </div>
    </div>
</div>

<?php else: ?>
<!-- ════════ PAYMENT FORM ════════ -->

<!-- Timeout overlay (shown by JS when countdown hits zero) -->
<div class="timeout-overlay" id="timeoutOverlay">
    <div class="timeout-box">
        <span class="timeout-icon">⏱️</span>
        <h2>Session expired</h2>
        <p>
            Your reservation hold has timed out and your table has been released.
            Please start a new reservation to try again.
        </p>
        <div class="status-actions" style="justify-content:center;">
            <a href="reservation_form.php" class="btn-primary">New reservation</a>
            <a href="index.php" class="btn-secondary">Home</a>
        </div>
    </div>
</div>

<div class="pay-wrap">

    <!-- Countdown timer bar -->
    <div class="pay-timer-bar">
        <span class="pay-timer-label">⏳ Hold expires in</span>
        <div class="pay-progress"><div class="pay-progress-fill" id="progressFill"></div></div>
        <span class="pay-timer-count" id="timerDisplay">5:00</span>
    </div>

    <?php if ($paymentError): ?>
    <div style="background:#fff3cd;border:0.5px solid #f0c040;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1rem;font-size:13px;color:#6b4c00;line-height:1.6;">
        ⚠️ <?= htmlspecialchars($paymentError) ?>
    </div>
    <?php endif; ?>

    <!-- Booking summary card -->
    <div class="pay-card">
        <div class="pay-section-label"><i class="ti ti-calendar-check"></i> Reservation summary</div>
        <div class="pay-summary-row">
            <span><?= htmlspecialchars($res['name']) ?> &mdash; <?= (int)$res['guests'] ?> guest(s)</span>
            <span><?= $formattedDate ?></span>
        </div>
        <div class="pay-summary-row"><span>Time</span><span><?= htmlspecialchars($res['time']) ?></span></div>
        <div class="pay-summary-row"><span>Table</span><span>#<?= (int)$res['table_number'] ?></span></div>
        <?php if (!empty($res['special_requests'])): ?>
        <div class="pay-summary-row">
            <span>Special requests</span>
            <span style="max-width:60%;text-align:right;"><?= htmlspecialchars($res['special_requests']) ?></span>
        </div>
        <?php endif; ?>
        <div class="pay-summary-row">
            <span><strong>Reservation deposit</strong></span>
            <span><strong>KES <?= number_format($depositAmount) ?></strong></span>
        </div>
    </div>

    <!-- Payment form card -->
    <div class="pay-card">
        <div class="pay-section-label"><i class="ti ti-lock"></i> Payment details</div>

        <form method="POST" action="payment.php" id="payForm">
            <input type="hidden" name="pay" value="1">
            <input type="hidden" name="pay_method" id="payMethodInput" value="card">

            <!-- Payment method tabs -->
            <div class="pay-methods">
                <button type="button" class="pay-method-btn active" onclick="switchMethod('card', this)">
                    💳 &nbsp;Card
                </button>
                <button type="button" class="pay-method-btn" onclick="switchMethod('mpesa', this)">
                    📱 &nbsp;M-Pesa
                </button>
            </div>

            <!-- Card fields -->
            <div id="cardFields">
                <div class="pay-field-group">
                    <label for="card_name">Cardholder name</label>
                    <input type="text" id="card_name" name="card_name"
                           placeholder="Jane Smith" autocomplete="cc-name">
                </div>
                <div class="pay-field-group">
                    <label for="card_number">Card number</label>
                    <input type="text" id="card_number" name="card_number"
                           placeholder="1234 5678 9012 3456" maxlength="19"
                           autocomplete="cc-number" oninput="formatCardNumber(this)">
                </div>
                <div class="pay-row">
                    <div class="pay-field-group">
                        <label for="card_expiry">Expiry (MM/YY)</label>
                        <input type="text" id="card_expiry" name="card_expiry"
                               placeholder="08/27" maxlength="5"
                               autocomplete="cc-exp" oninput="formatExpiry(this)">
                    </div>
                    <div class="pay-field-group">
                        <label for="card_cvv">CVV</label>
                        <input type="password" id="card_cvv" name="card_cvv"
                               placeholder="•••" maxlength="4" autocomplete="cc-csc">
                    </div>
                </div>
            </div>

            <!-- M-Pesa fields -->
            <div id="mpesaFields" style="display:none;">
                <div class="pay-field-group">
                    <label for="mpesa_phone">M-Pesa phone number</label>
                    <input type="tel" id="mpesa_phone" name="mpesa_phone"
                           placeholder="+254 7XX XXX XXX">
                </div>
                <p style="font-size:12px;color:#aaa;margin-top:-0.5rem;margin-bottom:1rem;line-height:1.6;">
                    You will receive an STK push on your phone. Enter your M-Pesa PIN to complete payment.
                </p>
            </div>

            <button type="submit" class="pay-submit" id="payBtn">
                Pay KES <?= number_format($depositAmount) ?> &rarr;
            </button>
            <p class="pay-secure-note">
                <i class="ti ti-shield-check"></i> Secured &amp; encrypted payment
            </p>
        </form>
    </div>

    <div style="text-align:center;font-size:12px;color:#bbb;padding-bottom:1.5rem;">
        <a href="reservation_form.php" style="color:#bbb;text-decoration:none;">Cancel &amp; start over</a>
    </div>
</div>

<script>
// ── Countdown timer ────────────────────────────────────────────
var remaining = <?= (int)$remaining ?>;
var total     = <?= (int)$PAYMENT_TIMEOUT ?>;
var timerEl   = document.getElementById('timerDisplay');
var fillEl    = document.getElementById('progressFill');
var overlay   = document.getElementById('timeoutOverlay');

function tick() {
    if (remaining <= 0) {
        timerEl.textContent = '0:00';
        fillEl.style.width  = '0%';
        fillEl.style.background = '#e05252';
        overlay.classList.add('show');
        // Fire-and-forget cancel request
        fetch('cancel_reservation.php', {
            method : 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body   : 'reservation_id=<?= (int)$res['reservation_id'] ?>'
        });
        return;
    }
    var m = Math.floor(remaining / 60);
    var s = remaining % 60;
    timerEl.textContent = m + ':' + (s < 10 ? '0' : '') + s;
    fillEl.style.width  = (remaining / total * 100) + '%';
    if (remaining <= 60) {
        timerEl.classList.add('urgent');
        fillEl.style.background = '#e05252';
    }
    remaining--;
    setTimeout(tick, 1000);
}
tick();

// ── Payment method toggle ──────────────────────────────────────
function switchMethod(method, btn) {
    document.querySelectorAll('.pay-method-btn').forEach(function(b) {
        b.classList.remove('active');
    });
    btn.classList.add('active');
    document.getElementById('payMethodInput').value = method;
    document.getElementById('cardFields').style.display  = method === 'card'  ? '' : 'none';
    document.getElementById('mpesaFields').style.display = method === 'mpesa' ? '' : 'none';
}

// ── Card number auto-spacing ───────────────────────────────────
function formatCardNumber(el) {
    var v = el.value.replace(/\D/g, '').substring(0, 16);
    el.value = v.replace(/(.{4})/g, '$1 ').trim();
}

// ── Expiry auto-slash ──────────────────────────────────────────
function formatExpiry(el) {
    var v = el.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 3) v = v.substring(0, 2) + '/' + v.substring(2);
    el.value = v;
}
</script>

<?php endif; ?>
</body>
</html>