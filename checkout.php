<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/catalog.php';

// The cart lives in the session, so it must be readable before the guard runs.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Single boxes and combo packs, priced from the shared catalogue
$products = shakti_cart_catalog();

// There is nothing to check out, so send the customer back to the cart rather
// than rendering a full order form with an empty summary and a zero total.
$checkoutCart = [];
foreach ($_SESSION['cart'] ?? [] as $productId => $quantity) {
    $quantity = (int) $quantity;
    if ($quantity > 0 && isset($products[$productId])) {
        $checkoutCart[$productId] = $quantity;
    }
}
if (empty($checkoutCart)) {
    header('Location: cart');
    exit;
}

$page_title = 'Checkout';
include 'includes/header.php';
?>

<!-- ===== CHECKOUT HERO ===== -->
<section class="checkout-hero">
  <div class="container">
    <p class="checkout-kicker">Secure checkout</p>
    <h1 class="checkout-hero-title">Almost there</h1>
    <p class="checkout-hero-sub">Complete your details and we’ll bring your protein snacks to your door.</p>
  </div>
</section>

<!-- ===== CHECKOUT SECTION ===== -->
<section class="checkout-section">
  <div class="container">
    <div class="checkout-grid">

      <!-- Checkout Form -->
      <div class="checkout-form-col">
        <div class="billing-details-card">
          <h3 class="checkout-section-title"><i class="bi bi-geo-alt-fill"></i> Delivery Details</h3>
          <p class="checkout-card-subtitle">Tell us where to deliver your Shakti Bites.</p>
          <form action="place-order" method="post" class="checkout-form" id="checkout-form">
            <div class="form-row">
              <div class="form-group">
                <label for="first-name">First Name *</label>
                <input type="text" id="first-name" autocomplete="given-name" name="first_name" placeholder="Enter your first name" required>
              </div>
              <div class="form-group">
                <label for="last-name">Last Name *</label>
                <input type="text" id="last-name" autocomplete="family-name" name="last_name" placeholder="Enter your last name" required>
              </div>
            </div>

            <div class="form-group">
              <label for="address">Address *</label>
              <textarea id="address" autocomplete="street-address" name="address" placeholder="Street address, apartment, suite, etc." rows="2" required></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" autocomplete="address-level2" name="city" placeholder="City" required>
              </div>
              <div class="form-group">
                <label for="state">State *</label>
                <input type="text" id="state" autocomplete="address-level1" name="state" placeholder="State" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="pincode">Pincode *</label>
                <input type="text" id="pincode" autocomplete="postal-code" name="pincode" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" placeholder="6-digit pincode" required>
              </div>
              <div class="form-group">
                <label for="phone">Phone *</label>
                <input type="tel" id="phone" autocomplete="tel" name="phone" placeholder="10-digit mobile number" required>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" autocomplete="email" name="email" placeholder="your@email.com" required>
            </div>

          <fieldset class="checkout-choice-group">
            <legend class="checkout-section-title">Account Options</legend>
            <p class="checkout-choice-help">Choose how you would like to check out.</p>
            <div class="payment-options" id="account-options">
              <label class="payment-option" data-account="guest">
                <input type="radio" name="account_type" value="guest" checked>
                <span class="payment-option-details">
                  <i class="bi bi-person-badge"></i> <strong>Guest checkout</strong>
                  <small>No signup needed. Just place your order.</small>
                </span>
              </label>
              <label class="payment-option" data-account="create">
                <input type="radio" name="account_type" value="create">
                <span class="payment-option-details">
                  <i class="bi bi-person-plus"></i> <strong>Create an Account</strong>
                  <small>Save your details for your next order.</small>
                </span>
              </label>
            </div>

            <div class="account-passwords" id="account-passwords" style="display:none;">
              <div class="form-row">
                <div class="form-group">
                  <label for="account_password">Password *</label>
                  <input type="password" id="account_password" name="password" placeholder="Minimum 6 characters" autocomplete="new-password">
                </div>
                <div class="form-group">
                  <label for="account_password_confirm">Confirm Password *</label>
                  <input type="password" id="account_password_confirm" name="confirm_password" placeholder="Re-type password" autocomplete="new-password">
                </div>
              </div>
            </div>

          </fieldset>
          <fieldset class="checkout-choice-group">
            <legend class="checkout-section-title">Payment Method</legend>
            <p class="checkout-choice-help">Select your preferred way to pay.</p>
            <div class="payment-options">
              <label class="payment-option" data-method="cod">
                <input type="radio" name="payment" value="cod" checked>
                <span class="payment-option-details">
                  <i class="bi bi-cash-stack"></i> <strong>Cash on Delivery</strong>
                  <small>Pay when you receive your order</small>
                </span>
              </label>
              <label class="payment-option" data-method="online">
                <input type="radio" name="payment" value="online">
                <span class="payment-option-details">
                  <i class="bi bi-credit-card"></i> <strong>Online Payment</strong>
                  <small>UPI / Card / Netbanking</small>
                </span>
              </label>
            </div>

            <div class="payment-note" id="payment-note">
              <i class="bi bi-shield-lock"></i> Your payment is processed securely by Razorpay.
            </div>

          </fieldset>

            <?php if (!empty($_SESSION['checkout_error'])): ?>
              <div class="alert alert-danger mt-3" role="alert">
                <?php echo htmlspecialchars($_SESSION['checkout_error']); unset($_SESSION['checkout_error']); ?>
              </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-place-order" id="place-order-btn">
              <i class="bi bi-lock-fill"></i> <span>Place Order</span>
            </button>
          </form>
        </div>
      </div>

<!-- Order Summary -->
      <div class="checkout-summary-col">
        <a class="checkout-edit-cart" href="cart"><span aria-hidden="true">&#8592;</span> Edit your cart</a>
        <div class="checkout-summary-heading">
          <h3>Your Order Summary</h3>
          <i class="bi bi-bag-check"></i>
        </div>
        <div class="checkout-items">
          <?php
          $subtotal = 0;
          foreach ($checkoutCart as $productId => $quantity) {
              $productPrice = $products[$productId]['price'];
              $itemTotal = $productPrice * $quantity;
              $subtotal += $itemTotal;
              ?>
              <div class="checkout-item">
                  <img class="checkout-item-image" src="assets/images/<?php echo htmlspecialchars($products[$productId]['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                  <div class="checkout-item-copy"><span><?php echo htmlspecialchars($products[$productId]['name']); ?></span><small>Quantity: <?php echo $quantity; ?></small></div>
                  <span>&#8377;<?php echo number_format($itemTotal, 0); ?></span>
              </div>
          <?php
          }
          ?>
        </div>
        <hr>
        <div class="checkout-summary-row">
            <span>Subtotal</span>
            <span>&#8377;<?php echo number_format($subtotal, 0); ?></span>
        </div>
        <div class="checkout-summary-row">
            <span>Shipping</span>
            <span style="color: var(--green);">Free</span>
        </div>
        <div class="checkout-summary-row">
            <span>Tax (GST)</span>
            <span>&#8377;<?php echo number_format($subtotal * 0.05, 0); ?></span>
        </div>
        <hr>
        <div class="checkout-total-row">
            <strong>Total</strong>
            <strong style="color: var(--orange);">&#8377;<?php echo number_format($subtotal * 1.05, 0); ?></strong>
        </div>

        <div class="checkout-guarantee">
            <i class="bi bi-shield-check"></i>
            <span>100% Secure Checkout</span>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
(function () {
    "use strict";
    var form = document.getElementById('checkout-form');
    var btn = document.getElementById('place-order-btn');
    var note = document.getElementById('payment-note');

    if (!form || !btn) { return; }

    function getTotalText() {
        var el = document.querySelector('.checkout-total-row strong:last-child');
        return el ? el.textContent.trim() : '';
    }

    function getPaymentMethod() {
        var checked = form.querySelector('input[name="payment"]:checked');
        return checked ? checked.value : 'cod';
    }

    function refreshButton() {
        var online = getPaymentMethod() === 'online';
        btn.querySelector('i').className = online ? 'bi bi-credit-card' : 'bi bi-lock-fill';
        btn.querySelector('span').textContent = online ? ('Pay ' + getTotalText() + ' Online') : 'Place Order';
        if (note) { note.style.display = online ? 'flex' : 'none'; }
    }

    function setButtonBusy(b) {
        btn.disabled = b;
        btn.querySelector('i').className = b ? 'bi bi-hourglass-split' : (getPaymentMethod() === 'online' ? 'bi bi-credit-card' : 'bi bi-lock-fill');
    }

    function loadRazorpayScript(callback) {
        if (typeof Razorpay !== 'undefined') { callback(); return; }
        var script = document.createElement('script');
        script.src = 'https://checkout.razorpay.com/v1/checkout.js';
        script.async = true;
        script.onload = callback;
        script.onerror = function () { alert('Failed to load the payment gateway. Please try again.'); setButtonBusy(false); };
        document.head.appendChild(script);
    }

    function startOnlinePayment() {
        setButtonBusy(true);
            var formData = new FormData(form);
            formData.set('payment', 'online');
            fetch('create-order', { method: 'POST', body: formData })
                .then(function (r) {
                    if (r.status === 401) { window.location.href = 'login'; return null; }
                    return r.json().then(function (d) { return { ok: r.ok, payload: d }; });
                })
                .then(function (res) {
                    if (res === null) { return; }
                    var data = res.payload;
                    if (!res.ok || !data.success) {
                        alert(data.message || 'Unable to start payment.');
                        setButtonBusy(false);
                        return;
                    }
                    loadRazorpayScript(function () { openRazorpay(data); });
                })
                .catch(function () { alert('Unable to start payment. Please try again.'); setButtonBusy(false); });
    }

    function openRazorpay(data) {
        if (typeof Razorpay === 'undefined') {
            alert('The payment gateway failed to load. Please try again.');
            setButtonBusy(false);
            return;
        }
        var options = {
            key: data.key_id,
            amount: data.amount,
            currency: data.currency,
            order_id: data.order_id,
            name: data.name || 'Shakti Bites',
            description: 'Shakti Bites order payment',
            handler: function (response) {
                var fd = new FormData();
                fd.append('razorpay_payment_id', response.razorpay_payment_id);
                fd.append('razorpay_order_id', response.razorpay_order_id);
                fd.append('razorpay_signature', response.razorpay_signature);
                fetch('verify-payment', { method: 'POST', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (v) {
                        if (v.status === 'success') {
                            window.location.href = 'order-complete';
                        } else {
                            alert(v.message || 'Payment verification failed.');
                            setButtonBusy(false);
                        }
                    })
                    .catch(function () { alert('Something went wrong during verification.'); setButtonBusy(false); });
            },
            theme: { color: '#ff6b35' }
        };
        var rzp = new Razorpay(options);
        rzp.open();
    }

    // Always intercept the submit. For online orders we run the AJAX flow;
    // for COD we fall through to the native, JavaScript-free form submission.
    form.addEventListener('submit', function (e) {
        if (getPaymentMethod() === 'online') {
            e.preventDefault();
            startOnlinePayment();
        }
    });

    form.addEventListener('change', function (e) {
        if (e.target && e.target.name === 'payment') { refreshButton(); }
        if (e.target && e.target.name === 'account_type') { refreshAccountFields(); }
    });

    var pwdBox = document.getElementById('account-passwords');
    var pwdFields = pwdBox ? pwdBox.querySelectorAll('input') : [];

    function getAccountType() {
        var checked = form.querySelector('input[name="account_type"]:checked');
        return checked ? checked.value : 'guest';
    }

    function refreshAccountFields() {
        var creating = getAccountType() === 'create';
        if (pwdBox) { pwdBox.style.display = creating ? 'block' : 'none'; }
        pwdFields.forEach(function (f) { f.required = creating; });
    }

    refreshAccountFields();
    refreshButton();
})();
</script>


<?php include 'includes/footer.php'; ?>
