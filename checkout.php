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
require_once __DIR__ . '/includes/checkout-pricing.php';
$codPricing = shakti_checkout_pricing($checkoutCart, $products, 'cod');
$onlinePricing = shakti_checkout_pricing($checkoutCart, $products, 'online');
$checkoutTotal = 0;
foreach ($checkoutCart as $productId => $quantity) $checkoutTotal += $products[$productId]['price'] * $quantity;
include 'includes/header.php';
?>

<!-- ===== CHECKOUT HERO ===== -->
<section class="checkout-launchpad"><a href="./"><img src="assets/images/logo.PNG" alt="Shakti Bites"></a><p>REAL INGREDIENTS. REAL ENERGY.</p><h1>Your goodness bag is ready.</h1><p>Complete your checkout to bring home your favourite bites.</p><button type="button" id="checkout-reopen">Continue to checkout &#8594;</button><a href="cart">&#8592; Back to your cart</a></section>
<div class="checkout-modal-backdrop" id="checkout-popup">
<div class="checkout-modal" role="dialog" aria-modal="true" aria-labelledby="checkout-popup-title" tabindex="-1">
<h2 id="checkout-popup-title" class="visually-hidden">Shakti Bites secure checkout</h2>
<main class="commerce-page commerce-checkout">
<section class="checkout-hero">
  <div class="container">
    <div class="quick-checkout-brand"><a href="cart" aria-label="Back to cart">&#8592;</a><a href="./"><img src="assets/images/logo.PNG" alt="Shakti Bites"></a><button type="button" id="checkout-close" aria-label="Close checkout">&times;</button></div>
    <div class="quick-checkout-banner">Real ingredients. Real energy. Delivered to your door.</div>
  </div>
</section>

<!-- ===== CHECKOUT SECTION ===== -->
<section class="checkout-section">
  <div class="container">
    <div class="checkout-grid">

      <!-- Checkout Form -->
      <div class="checkout-form-col">
        <div class="billing-details-card">
          <form action="place-order" method="post" class="checkout-form" id="checkout-form">
                        <?php if (!empty($_SESSION['checkout_error'])): ?>
              <div class="alert alert-danger mt-3" role="alert">
                <?php echo htmlspecialchars($_SESSION['checkout_error']); unset($_SESSION['checkout_error']); ?>
              </div>
            <?php endif; ?>
            <div id="checkout-phone-step" hidden>
              <h1>Enter mobile number</h1><p>Provide your mobile number to continue</p>
              <label class="phone-entry" for="checkout-mobile"><span>&#127470;&#127475; +91</span><input type="tel" id="checkout-mobile" autocomplete="tel-national" inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" placeholder="10-digit mobile number" aria-describedby="checkout-phone-error"></label>
              <p id="checkout-phone-error" role="status" aria-live="polite"></p>
              <button type="button" class="quick-next" id="checkout-phone-next">Continue &#8594;</button>
            </div>
            <fieldset id="checkout-address-step" class="quick-checkout-fields">
            <h2 class="checkout-section-title">Add shipping address</h2>
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
                <input type="tel" id="phone" autocomplete="tel-national" name="phone" inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" placeholder="10-digit mobile number" required>
              </div>
            </div>
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" autocomplete="email" name="email" placeholder="your@email.com" required>
            </div>
            <div class="quick-address-type"><span>Address type</span><label><input type="radio" name="address_type" value="home" checked> Home</label><label><input type="radio" name="address_type" value="office"> Office</label><label><input type="radio" name="address_type" value="other"> Others</label></div>
            <button type="button" class="quick-next" id="checkout-address-next" hidden>Add address</button>
            </fieldset>
            <div id="checkout-phone-confirmation" class="quick-phone-confirmation" hidden><span id="checkout-phone-display"></span><button type="button" id="checkout-phone-edit">Edit</button></div>
            <fieldset id="checkout-payment-step" class="quick-checkout-fields">
            <div id="checkout-address-confirmation" class="quick-address-confirmation" hidden><strong>Delivering to</strong><p id="checkout-address-display"></p><button type="button" id="checkout-address-edit">Edit address</button></div>


          <fieldset class="checkout-choice-group">
            <legend class="checkout-section-title"><span class="commerce-section-number">2</span> Your account</legend>
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
            <legend class="checkout-section-title"><span class="commerce-section-number">3</span> Payment method</legend>
            <p class="checkout-choice-help">Select your preferred way to pay.</p>
            <div class="payment-options">
              <label class="payment-option" data-method="cod">
                <input type="radio" name="payment" value="cod" checked>
                <span class="payment-option-details">
                  <i class="bi bi-cash-stack"></i> <strong>Cash on Delivery</strong>
                  <small>Pay on delivery &middot; &#8377;<?php echo $codPricing['cod_fee']; ?> COD fee</small>
                </span>
              </label>
              <label class="payment-option" data-method="online">
                <input type="radio" name="payment" value="online">
                <span class="payment-option-details">
                  <i class="bi bi-credit-card"></i> <strong>Online Payment</strong>
                  <small>UPI / Card / Netbanking &middot; Save &#8377;<?php echo $onlinePricing['prepaid_discount']; ?> extra</small>
                </span>
              </label>
            </div>

            <div class="payment-note" id="payment-note">
              <i class="bi bi-shield-lock"></i> Your payment is processed securely by Razorpay.
            </div>

          </fieldset>


            <button type="submit" class="btn btn-place-order" id="place-order-btn">
              <i class="bi bi-lock-fill"></i> <span>Place Order</span>
            </button>
            <p class="commerce-tax-note">By placing your order, you agree to our <a href="terms">terms</a> and <a href="privacy">privacy policy</a>.</p>
            </fieldset>
          </form>
        </div>
      </div>

<!-- Order Summary -->
      <div class="checkout-summary-col">
        <details class="quick-order-summary"><summary><span>Order summary <small>(<?php echo array_sum($checkoutCart); ?> items)</small></span><strong>&#8377;<?php echo number_format($checkoutTotal); ?> <span aria-hidden="true">&#8250;</span></strong></summary>
        <a class="checkout-edit-cart" href="cart">Edit your cart</a>
        <div class="checkout-items">
          <?php
          $subtotal = 0;
          $orderSavings = 0;
          foreach ($checkoutCart as $productId => $quantity) {
              $productPrice = $products[$productId]['price'];
              $itemTotal = $productPrice * $quantity;
              $subtotal += $itemTotal;
              $orderSavings += max(0, ($products[$productId]['original_price'] ?? $productPrice) - $productPrice) * $quantity;
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
        <?php if ($orderSavings > 0): ?><div class="commerce-order-saving"><i class="bi bi-tag" aria-hidden="true"></i> You're saving &#8377;<?php echo number_format($orderSavings); ?> on this order</div><?php endif; ?>
        <div class="checkout-price-breakdown">
          <table class="checkout-price-table" aria-label="Order price breakdown">
            <tbody>
              <tr><th scope="row">Subtotal</th><td>&#8377;<?php echo number_format($subtotal, 0); ?></td></tr>
              <tr><th scope="row">Shipping</th><td><?php echo $codPricing['shipping'] ? '&#8377;' . number_format($codPricing['shipping']) : 'Free'; ?></td></tr>
              <tr id="checkout-payment-adjustment"><th scope="row">COD fee</th><td>&#8377;<?php echo $codPricing['cod_fee']; ?></td></tr>
            </tbody>
            <tfoot><tr><th scope="row">Total</th><td data-checkout-total>&#8377;<?php echo number_format($codPricing['total'], 0); ?></td></tr></tfoot>
          </table>
        </div>

        <div class="checkout-guarantee">
            <i class="bi bi-shield-check"></i>
            <span>100% Secure Checkout</span>
        </div>
        <div class="commerce-payments"><span>UPI</span><span>VISA</span><span>Mastercard</span><span>COD</span></div>
        <a class="commerce-help" href="contact">Need a hand? We're here to help <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </details>
        <div class="checkout-prepaid-offer"><div><strong>&#10003; Extra &#8377;<?php echo number_format($onlinePricing['prepaid_discount']); ?> OFF on prepaid orders</strong><span>UPI / Card / Netbanking only</span></div><strong>&#8377;<?php echo number_format($onlinePricing['total']); ?><small>Online total</small></strong></div>
      </div>

    </div>
  </div>
</section>
<div class="quick-checkout-trust"><span>&#128274; Secure payments</span><span>&#128666; Delivered with care</span><span>&#10003; COD available</span></div>
<div class="quick-checkout-policies"><a href="terms">Terms &amp; Conditions</a><a href="privacy">Privacy Policy</a><a href="contact">Need help?</a></div>
</main>
</div>
</div>
<script src="assets/js/checkout-steps.js?v=20261014-popup" defer></script>

<script>
(function () {
    "use strict";
    var form = document.getElementById('checkout-form');
    var btn = document.getElementById('place-order-btn');
    var note = document.getElementById('payment-note');

    if (!form || !btn) { return; }

    function getTotalText() {
        var el = document.querySelector('[data-checkout-total]');
        return el ? el.textContent.trim() : '';
    }

    function getPaymentMethod() {
        var checked = form.querySelector('input[name="payment"]:checked');
        return checked ? checked.value : 'cod';
    }

    function refreshButton() {
        var online = getPaymentMethod() === 'online';
        var prices = <?php echo json_encode(['cod' => $codPricing, 'online' => $onlinePricing]); ?>;
        var price = online ? prices.online : prices.cod;
        var money = function (value) { return '\u20b9' + Number(value).toLocaleString('en-IN'); };
        document.querySelector('[data-checkout-total]').textContent = money(price.total);
        document.querySelector('.quick-order-summary summary strong').firstChild.textContent = money(price.total) + ' ';
        var adjustment = document.getElementById('checkout-payment-adjustment');
        adjustment.querySelector('th').textContent = online ? 'Prepaid discount' : 'COD fee';
        adjustment.querySelector('td').textContent = (online ? '\u2212' : '') + money(online ? price.prepaid_discount : price.cod_fee);
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
            modal: { ondismiss: function () { setButtonBusy(false); refreshButton(); } },
            theme: { color: '#cf5a0a' }
        };
        var rzp = new Razorpay(options);
        rzp.on('payment.failed', function () { setButtonBusy(false); refreshButton(); });
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
