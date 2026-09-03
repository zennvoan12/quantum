/**
 * QA screenshots: Guest → Pembeli → Admin
 * Usage: node scripts/qa_screenshots.mjs
 */
import puppeteer from 'puppeteer';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const OUT = path.resolve(process.env.SHOT_OUT || 'D:/obsidian folder/my project/TA/assets/screenshots/qa-2026-09-02');
const BASE = process.env.APP_URL || 'http://127.0.0.1:8000';

fs.mkdirSync(OUT, { recursive: true });

async function shot(page, name) {
  // Matikan fade-up supaya screenshot tidak menangkap opacity:0
  await page.addStyleTag({
    content: '.fade-up{opacity:1!important;animation:none!important;transform:none!important}',
  }).catch(() => {});
  await new Promise((r) => setTimeout(r, 200));
  const file = path.join(OUT, name);
  await page.screenshot({ path: file, fullPage: true });
  console.log('SHOT', name);
}

async function login(page, email, password) {
  await page.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded', timeout: 60000 });
  await page.waitForSelector('input[type="email"], input[name="email"]', { timeout: 15000 });
  // clear first
  await page.evaluate(() => {
    document.querySelectorAll('input[type="email"], input[name="email"], input[type="password"], input[name="password"]').forEach((el) => {
      el.value = '';
    });
  });
  await page.type('input[name="email"], input[type="email"]', email, { delay: 5 });
  await page.type('input[name="password"], input[type="password"]', password, { delay: 5 });
  await page.click('button[type="submit"]');
  await page.waitForFunction(
    () => !location.pathname.includes('/login'),
    { timeout: 60000 }
  );
  await page.waitForNetworkIdle({ idleTime: 500, timeout: 15000 }).catch(() => {});
}

async function logout(page) {
  await page.evaluate(async () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    await fetch('/logout', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token || '',
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `_token=${encodeURIComponent(token || '')}`,
      credentials: 'same-origin',
    });
  }).catch(() => {});
  await page.goto(`${BASE}/`, { waitUntil: 'networkidle2' });
}

const browser = await puppeteer.launch({
  headless: true,
  executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
  defaultViewport: { width: 1440, height: 900 },
  args: ['--no-sandbox', '--disable-dev-shm-usage'],
});
const page = await browser.newPage();

try {
  // ===== GUEST =====
  await page.goto(`${BASE}/`, { waitUntil: 'networkidle2' });
  await shot(page, '01-beranda.png');

  await page.goto(`${BASE}/produk`, { waitUntil: 'networkidle2' });
  await shot(page, '02-katalog.png');

  await page.goto(`${BASE}/produk/tempered-glass`, { waitUntil: 'networkidle2' });
  await shot(page, '03-produk-detail.png');

  await page.goto(`${BASE}/login`, { waitUntil: 'networkidle2' });
  await shot(page, '04-login.png');

  await page.goto(`${BASE}/register`, { waitUntil: 'networkidle2' });
  await shot(page, '05-register.png');

  // ===== PEMBELI =====
  await login(page, 'pembeli@quantum.com', 'password');
  await page.goto(`${BASE}/`, { waitUntil: 'networkidle2' });
  await shot(page, '02-beranda-login.png');

  // add to cart via POST (more reliable than clicking hidden/offscreen buttons)
  await page.goto(`${BASE}/produk/tempered-glass`, { waitUntil: 'networkidle2' });
  await page.evaluate(async () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const form = document.querySelector('form[action*="cart"]');
    const action = form?.action || '/cart';
    const body = new URLSearchParams();
    body.set('_token', token || '');
    const productInput = form?.querySelector('input[name="product_id"]');
    if (productInput) body.set('product_id', productInput.value);
    const qty = form?.querySelector('input[name="quantity"]');
    body.set('quantity', qty?.value || '1');
    await fetch(action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token || '',
        'Content-Type': 'application/x-www-form-urlencoded',
        Accept: 'text/html',
      },
      body: body.toString(),
      credentials: 'same-origin',
      redirect: 'follow',
    });
  }).catch(() => {});

  await page.goto(`${BASE}/cart`, { waitUntil: 'networkidle2' });
  await shot(page, '12-cart.png');

  await page.goto(`${BASE}/checkout`, { waitUntil: 'networkidle2' });
  await shot(page, '15-checkout.png');

  await page.goto(`${BASE}/profil/edit`, { waitUntil: 'networkidle2' });
  await shot(page, '11-profil-edit.png');

  await page.goto(`${BASE}/transaksi-saya`, { waitUntil: 'networkidle2' });
  await shot(page, '14-transaksi-saya.png');

  await logout(page);

  // ===== ADMIN =====
  await login(page, 'admin@quantum.com', 'password');
  await page.goto(`${BASE}/admin/dashboard`, { waitUntil: 'networkidle2' });
  await shot(page, '06-admin-dashboard.png');

  await page.goto(`${BASE}/admin/products`, { waitUntil: 'networkidle2' });
  await shot(page, '09-admin-products.png');

  await page.goto(`${BASE}/admin/transaksi`, { waitUntil: 'networkidle2' });
  await shot(page, '10-admin-transaksi.png');

  // open first order detail if link exists
  const detail = await page.$('a[href*="/admin/orders/"]');
  if (detail) {
    await Promise.all([
      detail.click(),
      page.waitForNavigation({ waitUntil: 'networkidle2' }),
    ]);
    await shot(page, '16-admin-transaksi-detail.png');
  }

  await page.goto(`${BASE}/admin/apriori`, { waitUntil: 'networkidle2' });
  await shot(page, '07-admin-apriori.png');

  const aprioriDetail = await page.$('a[href*="/admin/apriori/"]');
  if (aprioriDetail) {
    await Promise.all([
      aprioriDetail.click(),
      page.waitForNavigation({ waitUntil: 'networkidle2' }),
    ]);
    await shot(page, '08-admin-apriori-detail.png');
  } else {
    // run apriori once then screenshot detail
    const processBtn = await page.$('form[action*="apriori"] button[type="submit"]');
    if (processBtn) {
      await Promise.all([
        processBtn.click(),
        page.waitForNavigation({ waitUntil: 'networkidle2' }).catch(() => {}),
      ]);
      await shot(page, '07-admin-apriori.png');
      const link = await page.$('a[href*="/admin/apriori/"]');
      if (link) {
        await Promise.all([link.click(), page.waitForNavigation({ waitUntil: 'networkidle2' })]);
        await shot(page, '08-admin-apriori-detail.png');
      }
    }
  }

  // admin logout menu: buka dropdown akun lalu screenshot
  await page.goto(`${BASE}/admin/dashboard`, { waitUntil: 'networkidle2' });
  await page.evaluate(() => {
    const btn = [...document.querySelectorAll('button, a, summary')].find((el) =>
      /admin|akun|account|profil/i.test(el.textContent || '')
    );
    if (btn) btn.click();
  });
  await new Promise((r) => setTimeout(r, 400));
  await shot(page, '17-admin-with-logout.png');

  console.log('DONE', OUT);
} catch (e) {
  console.error('ERROR', e);
  process.exitCode = 1;
} finally {
  await browser.close();
}
