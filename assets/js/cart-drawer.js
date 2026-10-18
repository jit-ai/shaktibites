window.shaktiDeliveryProgress = function (root, pricing) {
  if (!root) return;
  const money = value => '\u20b9' + Number(value).toLocaleString('en-IN');
  root.querySelectorAll('[data-delivery-card]').forEach(card => {
    card.hidden = !pricing.boxes;
    card.querySelector('[data-delivery-title]').textContent = pricing.shipping_remaining > 0 ? 'Add ' + money(pricing.shipping_remaining) + ' more for FREE delivery' : "You've unlocked FREE delivery!";
    card.querySelector('[data-delivery-description]').textContent = pricing.shipping ? 'Add your favourite bites to save on shipping' : 'No shipping charges on this order';
    card.querySelector('[data-delivery-check]').hidden = pricing.shipping > 0;
    const track = card.querySelector('[role="progressbar"]');
    track.setAttribute('aria-valuenow', Math.min(499, pricing.subtotal));
    track.querySelector('span').style.width = pricing.shipping_progress + '%';
  });
};
document.addEventListener('DOMContentLoaded', () => {
  const drawer = document.getElementById('headerCartDrawer');
  if (!drawer || !drawer.showModal) return;
  const items = document.getElementById('headerCartItems');
  const status = document.getElementById('headerCartStatus');
  let token = '', busy = false, trigger = null;
  const money = value => '\u20b9' + Number(value).toLocaleString('en-IN');
  function element(tag, className, text) {
    const node = document.createElement(tag);
    node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
  }
  function button(label, text, action, id, quantity) {
    const node = element('button', '', text);
    node.type = 'button'; node.setAttribute('aria-label', label);
    node.dataset.action = action; node.dataset.id = id; node.dataset.quantity = quantity;
    return node;
  }
  function render(data) {
    token = data.token;
    items.replaceChildren();
    data.items.forEach(product => {
      const card = element('article', 'header-cart-item');
      const image = element('img', 'header-cart-image');
      image.src = 'assets/images/' + encodeURIComponent(product.image); image.alt = product.name;
      const copy = element('div', 'header-cart-copy');
      copy.append(element('h4', '', product.name), element('span', 'header-cart-tag', product.type === 'single' ? '6 energy bites / box' : 'Combo pack'));
      const prices = element('div', 'header-cart-prices');
      prices.append(element('strong', '', money(product.price)));
      if (product.original_price > product.price) {
        prices.append(element('s', '', money(product.original_price)), element('span', '', Math.round((1 - product.price / product.original_price) * 100) + '% off'));
      }
      const controls = element('div', 'header-cart-controls');
      const stepper = element('div', 'header-cart-stepper');
      stepper.append(button('Remove one ' + product.name, '\u2212', 'quantity', product.id, product.quantity - 1), element('span', '', product.quantity), button('Add one ' + product.name, '+', 'quantity', product.id, product.quantity + 1));
      stepper.lastElementChild.disabled = product.quantity >= 99;
      const remove = button('Remove ' + product.name + ' from cart', '', 'quantity', product.id, 0);
      remove.className = 'header-cart-remove';
      const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      icon.setAttribute('viewBox', '0 0 24 24'); icon.setAttribute('width', '20'); icon.setAttribute('height', '20'); icon.setAttribute('aria-hidden', 'true');
      const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
      path.setAttribute('d', 'M4 6h16M9 6V3h6v3M6 6l1 15h10l1-15M10 10v7m4-7v7');
      path.setAttribute('fill', 'none'); path.setAttribute('stroke', 'currentColor'); path.setAttribute('stroke-width', '1.6'); path.setAttribute('stroke-linecap', 'round'); path.setAttribute('stroke-linejoin', 'round');
      icon.append(path); remove.append(icon);
      controls.append(stepper, remove); copy.append(prices, controls); card.append(image, copy); items.append(card);
    });
    if (!data.items.length) {
      const empty = element('div', 'header-cart-empty');
      empty.append(element('i', 'bi bi-bag-heart'), element('h3', '', 'Your cart is waiting for goodness.'), element('p', '', 'Add your favourite bites to get started.'));
      items.append(empty);
    }
    document.getElementById('headerCartCount').textContent = data.cart_count;
    document.getElementById('headerCartTotal').textContent = money(data.online.total);
    document.getElementById('headerCartSaving').textContent = 'You save ' + money(data.savings + data.online.prepaid_discount) + ' on this order with online payment';
    document.getElementById('bagCodAlternative').textContent = data.cart_count ? 'or ' + money(data.cod.total) + ' with COD' : 'Your bag is empty';
    document.getElementById('bagOrderLabel').textContent = 'Order total (' + data.online.boxes + (data.online.boxes === 1 ? ' box)' : ' boxes)');
    document.getElementById('bagOriginalTotal').textContent = money(data.original_total);
    document.getElementById('bagSingleDiscount').textContent = '\u2212 ' + money(data.single_savings);
    document.getElementById('bagComboDiscount').textContent = '\u2212 ' + money(data.combo_savings);
    document.getElementById('bagSingleRow').hidden = !data.single_savings;
    document.getElementById('bagComboRow').hidden = !data.combo_savings;
    document.getElementById('bagPrepaidDiscount').textContent = '\u2212 ' + money(data.online.prepaid_discount);
    document.getElementById('bagCodFee').textContent = '+ ' + money(data.cod.cod_fee);
    document.getElementById('bagShipping').textContent = data.online.shipping ? money(data.online.shipping) : 'Free';
    window.shaktiDeliveryProgress(drawer, data.online);
    document.getElementById('bagOnlineTotal').textContent = money(data.online.total);
    document.getElementById('bagCodTotal').textContent = money(data.cod.total);
    ['bagPriceSummary', 'bagDelivery', 'headerCartSaving'].forEach(id => document.getElementById(id).hidden = !data.cart_count);
    const next = document.getElementById('headerCartContinue');
    next.href = data.cart_count ? 'checkout' : 'shop';
    next.firstChild.textContent = data.cart_count ? 'Continue ' : 'Shop bites ';
    document.querySelectorAll('[id^="cart-badge"], .sb-nav-cart-count').forEach(badge => {
      badge.textContent = data.cart_count; badge.classList.toggle('d-none', data.cart_count === 0);
    });
    document.querySelectorAll('[data-cart-count-text]').forEach(node => node.textContent = data.cart_count + ' items');
  }
  async function refresh() {
    const response = await fetch('cart-state', {cache: 'no-store'});
    if (!response.ok) throw new Error('Unable to load your cart. Please try again.');
    render(await response.json());
  }
  document.querySelectorAll('[data-cart-open]').forEach(link => link.addEventListener('click', async event => {
    event.preventDefault(); trigger = link;
    const mobile = document.getElementById('sbMobileNav');
    if (window.bootstrap && mobile) {
      const menu = bootstrap.Offcanvas.getInstance(mobile); if (menu) menu.hide();
    }
    drawer.showModal(); document.body.classList.add('header-cart-open');
    // Prevent stale items from being edited while the current session loads.
    busy = true; items.querySelectorAll('button').forEach(node => node.disabled = true);
    status.textContent = 'Loading your cart...';
    try { await refresh(); status.textContent = ''; }
    catch (error) { status.textContent = error.message; }
    finally { busy = false; }
  }));
  drawer.querySelector('[data-cart-close]').addEventListener('click', () => drawer.close());
  drawer.addEventListener('click', event => { if (event.target === drawer) { const rect = drawer.getBoundingClientRect(); if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) drawer.close(); } });
  drawer.addEventListener('close', () => {
    document.body.classList.remove('header-cart-open');
    if (trigger) trigger.focus();
  });
  items.addEventListener('click', async event => {
    const control = event.target.closest('button[data-action]');
    if (!control || busy) return;
    busy = true; items.querySelectorAll('button').forEach(node => node.disabled = true);
    status.textContent = 'Updating your cart...';
    try {
      const response = await fetch('update_cart', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams({product_id:control.dataset.id, quantity:control.dataset.quantity, token})});
      const result = await response.json();
      if (!response.ok || !result.ok) throw new Error(result.message || 'Unable to update your cart.');
      // Keep the full cart in sync when the drawer is used on that page.
      const current = location.pathname.replace(/\/$/, '').split('/').pop();
      if (current === 'checkout') { location.reload(); return; }
      if (current === 'cart') {
        const row = document.querySelector('.cart-item[data-product-id="' + control.dataset.id + '"]');
        if (row) {
          if (Number(control.dataset.quantity) === 0) row.remove();
          else row.querySelector('[id^="qty-"]').textContent = control.dataset.quantity;
        }
        if (!result.cart_count) { location.reload(); return; }
        if (typeof window.updateCartSummary === 'function') window.updateCartSummary(result);
      }
      await refresh(); status.textContent = 'Cart updated.';
      const replacement = items.querySelector('button[data-id="' + control.dataset.id + '"][data-quantity="' + control.dataset.quantity + '"]');
      if (replacement) replacement.focus();
      else drawer.querySelector('[data-cart-close]').focus();
    } catch (error) {
      status.textContent = error.message;
      items.querySelectorAll('button').forEach(node => node.disabled = false);
    } finally { busy = false; }
  });
});
