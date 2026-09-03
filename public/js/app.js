document.addEventListener('DOMContentLoaded', function () {

  /* ── Toast ─────────────────────────────────────────── */
  window.showToast = function (message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const colors = {
      info: 'border-neutral-200 bg-white text-neutral-900',
      success: 'border-green-200 bg-green-50 text-green-800',
      error: 'border-red-200 bg-red-50 text-red-800',
    };

    const toast = document.createElement('div');
    toast.className = `toast-item fixed bottom-4 left-1/2 -translate-x-1/2 z-[80] ${colors[type] || colors.info} border shadow-md px-4 py-3 rounded text-xs uppercase tracking-[0.15em] flex items-center gap-3 max-w-sm w-full`;
    toast.style.cssText = 'animation:fadeUp .3s ease';
    toast.innerHTML = `
      <span class="flex-1">${message}</span>
      <button onclick="this.closest('.toast-item').remove()" class="text-neutral-400 hover:text-neutral-700 text-lg leading-none">&times;</button>
    `;
    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity .3s';
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  };

  /* ── Motion helpers ────────────────────────────────── */
  const EASE_SMOOTH = [0.22, 1, 0.36, 1];
  const drawerEls = [document.getElementById('cart-drawer'), document.getElementById('wishlist-drawer')];
  const modalEls = ['quick-view-modal', 'estimasi-ongkir-modal', 'cancel-checkout-modal', 'otp-modal']
    .map(id => document.getElementById(id)).filter(Boolean);

  function openDrawer(el) {
    if (!el) return;
    el.classList.remove('translate-x-full');
    if (window.animate) {
      window.animate(el, { transform: ['translateX(100%)', 'translateX(0%)'] }, { duration: 0.32, easing: EASE_SMOOTH });
    }
  }

  function closeDrawer(el, done) {
    if (!el) return;
    if (window.animate) {
      window.animate(el, { transform: ['translateX(0%)', 'translateX(100%)'] }, { duration: 0.24, easing: EASE_SMOOTH })
        .finished.then(() => { el.classList.add('translate-x-full'); if (done) done(); });
    } else {
      el.classList.add('translate-x-full');
      if (done) done();
    }
  }

  function openModal(el) {
    if (!el) return;
    el.classList.remove('hidden');
    const box = el.firstElementChild;
    if (window.animate) {
      window.animate(el, { opacity: [0, 1] }, { duration: 0.2 });
      window.animate(box, { opacity: [0, 1], scale: [0.94, 1], y: [16, 0] }, { duration: 0.28, easing: EASE_SMOOTH });
    }
  }

  function closeModal(el) {
    if (!el) return;
    if (window.animate) {
      window.animate(el, { opacity: [1, 0] }, { duration: 0.18 }).finished
        .then(() => el.classList.add('hidden'));
    } else {
      el.classList.add('hidden');
    }
  }

  /* ── Cart count badge update + pulse ───────────────── */
  function updateCartBadge(count) {
    const badge = document.getElementById('cart-count-badge');
    if (!badge) return;
    badge.textContent = count;
    badge.classList.toggle('hidden', !count || count === 0);
    if (window.animate && count > 0) {
      window.animate(badge, { scale: [1, 1.6, 1] }, { duration: 0.4, easing: 'spring' });
    }
  }

  /* ── Cart Drawer ───────────────────────────────────── */
  const cartOpenBtn  = document.getElementById('open-cart-drawer');
  const cartCloseBtn = document.getElementById('close-cart-drawer');
  const cartDrawer   = document.getElementById('cart-drawer');

  if (cartOpenBtn && cartDrawer) {
    cartOpenBtn.addEventListener('click', e => { e.preventDefault(); openDrawer(cartDrawer); });
  }
  if (cartCloseBtn && cartDrawer) {
    cartCloseBtn.addEventListener('click', () => closeDrawer(cartDrawer));
    cartDrawer.addEventListener('click', e => { if (e.target === cartDrawer) closeDrawer(cartDrawer); });
  }

  /* ── Wishlist Drawer ───────────────────────────────── */
  const wlOpenBtn  = document.getElementById('open-wishlist-drawer');
  const wlCloseBtn = document.getElementById('close-wishlist-drawer');
  const wlDrawer   = document.getElementById('wishlist-drawer');

  if (wlOpenBtn && wlDrawer) {
    wlOpenBtn.addEventListener('click', e => { e.preventDefault(); openDrawer(wlDrawer); });
  }
  if (wlCloseBtn && wlDrawer) {
    wlCloseBtn.addEventListener('click', () => closeDrawer(wlDrawer));
    wlDrawer.addEventListener('click', e => { if (e.target === wlDrawer) closeDrawer(wlDrawer); });
  }

  /* ── Quick View Modal ──────────────────────────────── */
  const qvModal   = document.getElementById('quick-view-modal');
  const qvCloseBtn = document.getElementById('close-quick-view');
  if (qvCloseBtn && qvModal) {
    qvCloseBtn.addEventListener('click', () => closeModal(qvModal));
    qvModal.addEventListener('click', e => { if (e.target === qvModal) closeModal(qvModal); });
  }
  document.addEventListener('click', e => {
    const trigger = e.target.closest('[data-quickview]');
    if (!trigger) return;
    const modal = document.getElementById('quick-view-modal');
    if (!modal) return;
    modal.querySelector('[data-qv-name]').textContent  = trigger.dataset.name;
    modal.querySelector('[data-qv-price]').textContent  = 'Rp ' + new Intl.NumberFormat('id-ID').format(trigger.dataset.price);
    modal.querySelector('[data-qv-img]').src            = trigger.dataset.img;
    modal.querySelector('[data-qv-link]').href          = '/produk/' + trigger.dataset.slug;
    modal.querySelector('[data-qv-cart]').dataset.productId = trigger.dataset.quickview;
    openModal(modal);
  });

  /* ── Delete Confirm (keranjang) ───────────────────── */
  document.addEventListener('submit', e => {
    const btn = e.target.querySelector('[data-confirm-delete]');
    if (!btn) return;
    if (!confirm('Yakin hapus item ini dari keranjang?')) e.preventDefault();
  });

  /* ── Generic modals (estimasi ongkir, cancel, otp) ─── */
  modalEls.forEach(m => {
    if (m.id === 'quick-view-modal') return;
    m.addEventListener('click', e => { if (e.target === m) closeModal(m); });
    m.querySelectorAll('.btn-close-modal').forEach(b => b.addEventListener('click', () => closeModal(m)));
  });
  const estimasiBtn = document.getElementById('open-estimasi-ongkir');
  if (estimasiBtn) estimasiBtn.addEventListener('click', () => openModal(document.getElementById('estimasi-ongkir-modal')));
  const cancelBtn = document.getElementById('cancel-checkout');
  if (cancelBtn) cancelBtn.addEventListener('click', () => openModal(document.getElementById('cancel-checkout-modal')));
  const otpBtn = document.getElementById('btn-request-otp');
  if (otpBtn) otpBtn.addEventListener('click', () => openModal(document.getElementById('otp-modal')));

  /* ── AJAX cart count refresh ───────────────────────── */
  function refreshCart(animate = false) {
    if (!document.getElementById('cart-count-badge')) return;
    fetch('/cart/count', { headers: { 'Accept': 'application/json' } })
      .then(r => r.json())
      .then(d => {
        const count = d.count || 0;
        document.getElementById('drawer-count')?.replaceChildren(document.createTextNode(count));
        updateCartBadge(count);
      })
      .catch(() => {});
  }

  /* ── Wishlist Toggle ──────────────────────────────── */
  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-wishlist-toggle]');
    if (!btn) return;
    const productId = btn.dataset.wishlistToggle;
    const icon = btn.querySelector('svg');
    fetch('/wishlist/toggle/' + productId, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
    })
    .then(r => r.json())
    .then(data => {
      if (icon) {
        icon.classList.toggle('fill-current', data.wished);
        icon.classList.toggle('text-red-500', data.wished);
      }
      showToast(data.message, 'success');
    })
    .catch(() => showToast('Gagal menyimpan ke wishlist', 'error'));
  });

  /* ── Quick Add to Cart from quick-view ────────────── */
  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-qv-cart]');
    if (!btn) return;
    const productId = btn.dataset.productId;
    fetch('/cart', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'product_id=' + productId + '&quantity=1',
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast('Ditambahkan ke keranjang', 'success');
        updateCartBadge(data.count);
        setTimeout(() => closeModal(document.getElementById('quick-view-modal')), 400);
      } else {
        showToast(data.message || 'Gagal menambahkan', 'error');
      }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
  });

  /* ── Add to Cart (inline) ─────────────────────────── */
  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-add-cart]');
    if (!btn) return;
    const productId = btn.dataset.addCart;
    const qty = document.querySelector('[name="quantity"]')?.value || 1;
    fetch('/cart', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'product_id=' + productId + '&quantity=' + qty,
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast('Ditambahkan ke keranjang', 'success');
        updateCartBadge(data.count);
      } else {
        showToast(data.message || 'Gagal menambahkan', 'error');
      }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
  });

  /* ── Init badge on load ────────────────────────────── */
  refreshCart(false);
});

/* ── CSS Keyframes ──────────────────────────────────── */
if (!document.getElementById('fadeup-style')) {
  const style = document.createElement('style');
  style.id = 'fadeup-style';
  style.textContent = `@keyframes fadeUp { from { opacity:0; transform: translate(-50%, 10px); } to { opacity:1; transform: translate(-50%, 0); } }`;
  document.head.appendChild(style);
}
