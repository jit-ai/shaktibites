document.addEventListener('DOMContentLoaded', function () {
  const dialog = document.getElementById('comboBuilder');
  if (!dialog) return;
  const form = document.getElementById('comboBuilderForm');
  const cards = Array.from(dialog.querySelectorAll('.builder-product'));
  const plans = {3: {title:'Starter', discount:8, saving:91}, 6:{title:'Popular', discount:15, saving:341}, 9:{title:'Value', discount:20, saving:682}};
  const limit = Number(document.getElementById('builderBoxes').value);
  const el = id => document.getElementById(id);
  const money = value => '\u20b9' + value.toLocaleString('en-IN');
  const count = () => cards.reduce((sum, card) => sum + Number(card.querySelector('input').value), 0);
  function render() {
    let subtotal = 0, selected = count();
    const thumbnails = [];
    cards.forEach(card => {
      const input = card.querySelector('input');
      const quantity = Number(input.value);
      subtotal += quantity * Number(card.dataset.price);
      card.querySelector('[data-change="-1"]').disabled = quantity === 0;
      card.querySelector('[data-change="1"]').disabled = selected >= limit;
      if (quantity) {
        const item = document.createElement('div');
        const image = card.querySelector('img').cloneNode(); image.alt = '';
        const label = document.createElement('p'); label.textContent = card.querySelector('h3').textContent + ' × ' + quantity;
        item.append(image, label); thumbnails.push(item);
      }
    });
    const total = Math.round(subtotal * (100 - plans[limit].discount) / 100);
    el('builderSelected').replaceChildren(...thumbnails);
    el('builderCount').textContent = selected + ' / ' + limit + ' boxes selected';
    el('builderBites').textContent = selected * 6;
    el('builderProtein').textContent = selected * 60;
    el('builderSubtotalLabel').textContent = 'Subtotal (' + selected + ' boxes)';
    el('builderSubtotal').textContent = money(subtotal);
    el('builderSaving').textContent = 'You Save ' + money(subtotal - total) + ' (' + plans[limit].discount + '%)';
    el('builderTotal').textContent = el('builderButtonTotal').textContent = money(total);
    dialog.querySelector('.builder-add').disabled = selected !== limit;
    el('builderMessage').textContent = selected === limit ? 'Your combo is ready.' : 'Choose ' + (limit - selected) + ' more ' + (limit - selected === 1 ? 'box' : 'boxes') + ' to complete your combo.';
  }
  cards.forEach(card => {
    const input = card.querySelector('input');
    card.querySelectorAll('[data-change]').forEach(button => button.addEventListener('click', () => {
      const value = Number(input.value) + Number(button.dataset.change);
      if (value >= 0 && count() + Number(button.dataset.change) <= limit) { input.value = value; render(); }
    }));
    input.addEventListener('input', () => {
      const others = cards.filter(item => item !== card).reduce((sum, item) => sum + Number(item.querySelector('input').value), 0);
      input.value = Math.max(0, Math.min(limit - others, Math.floor(Number(input.value) || 0))); render();
    });
  });
  dialog.querySelector('.builder-clear').addEventListener('click', () => { cards.forEach(card => card.querySelector('input').value = 0); render(); });
  render();
  form.addEventListener('submit', event => { if (count() !== limit) { event.preventDefault(); render(); } });
});
