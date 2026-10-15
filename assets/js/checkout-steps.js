document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('checkout-form');
  if (!form) return;
  const popup = document.getElementById('checkout-popup');
  const modal = popup.querySelector('.checkout-modal');
  const launcher = document.querySelector('.checkout-launchpad');
  function openPopup() {
    popup.hidden = false;
    launcher.inert = true;
    document.body.classList.add('checkout-popup-open');
    modal.focus();
  }
  function closePopup() {
    popup.hidden = true;
    launcher.inert = false;
    document.body.classList.remove('checkout-popup-open');
    document.getElementById('checkout-reopen').focus();
  }
  document.getElementById('checkout-close').addEventListener('click', closePopup);
  document.getElementById('checkout-reopen').addEventListener('click', openPopup);
  popup.addEventListener('click', event => { if (event.target === popup) closePopup(); });
  document.addEventListener('keydown', event => {
    if (popup.hidden) return;
    // Let the payment provider manage its own keyboard interaction when open.
    const gateway = document.querySelector('.razorpay-container');
    if (gateway && gateway.getClientRects().length) return;
    if (event.key === 'Escape') { event.preventDefault(); closePopup(); }
    if (event.key === 'Tab') {
      const controls = Array.from(modal.querySelectorAll('a[href], button, input, textarea, summary, [tabindex="0"]')).filter(node => !node.disabled && !node.closest('[hidden]') && !node.closest('fieldset:disabled') && node.getClientRects().length);
      const first = controls[0], last = controls[controls.length - 1];
      if (event.shiftKey && (document.activeElement === first || document.activeElement === modal)) { event.preventDefault(); last.focus(); }
      else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    }
  });
  const mobile = document.getElementById('checkout-mobile');
  const phoneStep = document.getElementById('checkout-phone-step');
  const addressStep = document.getElementById('checkout-address-step');
  const paymentStep = document.getElementById('checkout-payment-step');
  const confirmation = document.getElementById('checkout-phone-confirmation');
  const phone = document.getElementById('phone');
  const error = document.getElementById('checkout-phone-error');
  let stage = 'phone';
  function show(next) {
    stage = next;
    document.body.dataset.checkoutStage = next;
    phoneStep.hidden = next !== 'phone';
    addressStep.hidden = next !== 'address';
    // Completed address fields remain enabled so they are submitted for payment.
    addressStep.disabled = next === 'phone';
    paymentStep.hidden = next !== 'payment';
    paymentStep.disabled = next !== 'payment';
    confirmation.hidden = next === 'phone';
    document.getElementById('checkout-phone-display').textContent = '+91 ' + phone.value;
  }
  function proceedPhone() {
    if (!/^[6-9][0-9]{9}$/.test(mobile.value)) {
      error.textContent = 'Enter a valid 10-digit mobile number.';
      mobile.focus(); return;
    }
    error.textContent = '';
    phone.value = mobile.value;
    show('address');
    document.getElementById('first-name').focus();
  }
  document.getElementById('checkout-phone-next').addEventListener('click', proceedPhone);
  mobile.addEventListener('input', () => {
    mobile.value = mobile.value.replace(/\D/g, '').slice(0, 10);
    error.textContent = '';
    if (mobile.value.length === 10) proceedPhone();
  });
  mobile.addEventListener('keydown', event => { if (event.key === 'Enter') { event.preventDefault(); proceedPhone(); } });
  document.getElementById('checkout-address-next').hidden = false;
  document.getElementById('checkout-address-next').addEventListener('click', () => {
    const invalid = Array.from(addressStep.querySelectorAll('input, textarea')).find(input => !input.checkValidity());
    if (invalid) { invalid.reportValidity(); return; }
    document.getElementById('checkout-address-display').textContent = [form.elements.first_name.value + ' ' + form.elements.last_name.value, form.elements.address.value, form.elements.city.value, form.elements.state.value, form.elements.pincode.value].join(', ');
    document.getElementById('checkout-address-confirmation').hidden = false;
    show('payment');
    paymentStep.scrollIntoView({behavior:'smooth', block:'start'});
  });
  document.getElementById('checkout-phone-edit').addEventListener('click', () => { mobile.value = phone.value; show('phone'); mobile.focus(); });
  document.getElementById('checkout-address-edit').addEventListener('click', () => { show('address'); document.getElementById('first-name').focus(); });
  phone.addEventListener('input', () => { document.getElementById('checkout-phone-display').textContent = '+91 ' + phone.value; });
  form.addEventListener('submit', event => { if (stage !== 'payment') { event.preventDefault(); event.stopImmediatePropagation(); } }, true);
  show('phone');
  openPopup();
});
