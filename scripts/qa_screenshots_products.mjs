/**
 * Re-capture product-heavy pages after image attach (no fade-up washout).
 */
import puppeteer from 'puppeteer';
import fs from 'fs';
import path from 'path';

const OUT = path.resolve(process.env.SHOT_OUT || 'D:/obsidian folder/my project/TA/assets/screenshots/qa-2026-09-02');
const BASE = process.env.APP_URL || 'http://127.0.0.1:8000';
fs.mkdirSync(OUT, { recursive: true });

async function shot(page, name) {
  await page.addStyleTag({
    content: '.fade-up{opacity:1!important;animation:none!important;transform:none!important}',
  }).catch(() => {});
  await new Promise((r) => setTimeout(r, 250));
  await page.screenshot({ path: path.join(OUT, name), fullPage: true });
  console.log('SHOT', name);
}

async function login(page, email, password) {
  await page.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded', timeout: 60000 });
  await page.evaluate((em, pw) => {
    const emailEl = document.querySelector('input[name="email"], input[type="email"]');
    const passEl = document.querySelector('input[name="password"], input[type="password"]');
    if (emailEl) emailEl.value = em;
    if (passEl) passEl.value = pw;
  }, email, password);
  await Promise.all([
    page.click('button[type="submit"]'),
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 60000 }).catch(() => {}),
  ]);
}

const browser = await puppeteer.launch({
  headless: true,
  executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
  defaultViewport: { width: 1440, height: 900 },
  args: ['--no-sandbox', '--disable-dev-shm-usage'],
});
const page = await browser.newPage();

try {
  await page.goto(`${BASE}/`, { waitUntil: 'networkidle2' });
  await shot(page, '01-beranda.png');

  await page.goto(`${BASE}/produk`, { waitUntil: 'networkidle2' });
  await shot(page, '02-katalog.png');

  await page.goto(`${BASE}/produk/tempered-glass`, { waitUntil: 'networkidle2' });
  await shot(page, '03-produk-detail.png');

  await login(page, 'admin@quantum.com', 'password');
  await page.goto(`${BASE}/admin/products`, { waitUntil: 'networkidle2' });
  await shot(page, '09-admin-products.png');

  console.log('DONE product pages', OUT);
} catch (e) {
  console.error('ERROR', e);
  process.exitCode = 1;
} finally {
  await browser.close();
}
