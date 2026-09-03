/**
 * Continue admin screenshots only (after guest/pembeli already captured).
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
  await new Promise((r) => setTimeout(r, 200));
  await page.screenshot({ path: path.join(OUT, name), fullPage: true });
  console.log('SHOT', name);
}

async function login(page, email, password) {
  await page.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded', timeout: 60000 });
  await page.waitForSelector('input[name="email"], input[type="email"]', { timeout: 15000 });
  await page.evaluate((em, pw) => {
    const emailEl = document.querySelector('input[name="email"], input[type="email"]');
    const passEl = document.querySelector('input[name="password"], input[type="password"]');
    if (emailEl) {
      emailEl.focus();
      emailEl.value = em;
      emailEl.dispatchEvent(new Event('input', { bubbles: true }));
    }
    if (passEl) {
      passEl.focus();
      passEl.value = pw;
      passEl.dispatchEvent(new Event('input', { bubbles: true }));
    }
  }, email, password);
  await Promise.all([
    page.click('button[type="submit"]'),
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 60000 }).catch(() => {}),
  ]);
  if (page.url().includes('/login')) {
    await shot(page, 'debug-admin-login-failed.png');
    throw new Error('Admin still on login page: ' + page.url());
  }
}

const browser = await puppeteer.launch({
  headless: true,
  executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
  defaultViewport: { width: 1440, height: 900 },
  args: ['--no-sandbox', '--disable-dev-shm-usage'],
});
const page = await browser.newPage();
page.setDefaultTimeout(60000);

try {
  await login(page, 'admin@quantum.com', 'password');

  await page.goto(`${BASE}/admin/dashboard`, { waitUntil: 'domcontentloaded', timeout: 60000 });
  await shot(page, '06-admin-dashboard.png');
  await shot(page, '17-admin-with-logout.png');

  await page.goto(`${BASE}/admin/products`, { waitUntil: 'domcontentloaded', timeout: 60000 });
  await shot(page, '09-admin-products.png');

  await page.goto(`${BASE}/admin/transaksi`, { waitUntil: 'domcontentloaded', timeout: 60000 });
  await shot(page, '10-admin-transaksi.png');

  const detailHref = await page.$eval('a[href*="/admin/orders/"]', (a) => a.href).catch(() => null);
  if (detailHref) {
    await page.goto(detailHref, { waitUntil: 'domcontentloaded', timeout: 60000 });
    await shot(page, '16-admin-transaksi-detail.png');
  }

  await page.goto(`${BASE}/admin/apriori`, { waitUntil: 'domcontentloaded', timeout: 60000 });
  await shot(page, '07-admin-apriori.png');

  // try process apriori
  const action = await page.$eval('form[action*="apriori"]', (f) => f.action).catch(() => null);
  if (action) {
    await page.evaluate(async (url) => {
      const token = document.querySelector('meta[name="csrf-token"]')?.content;
      const body = new URLSearchParams();
      body.set('_token', token || '');
      const minS = document.querySelector('input[name="min_support"]');
      const minC = document.querySelector('input[name="min_confidence"]');
      if (minS) body.set('min_support', minS.value || '0.02');
      if (minC) body.set('min_confidence', minC.value || '0.5');
      await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token || '', 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString(),
        credentials: 'same-origin',
        redirect: 'follow',
      });
    }, action);
    await page.goto(`${BASE}/admin/apriori`, { waitUntil: 'domcontentloaded', timeout: 60000 });
    await shot(page, '07-admin-apriori.png');
  }

  const detailApriori = await page.$$eval('a[href]', (as) => {
    const hit = as.find((a) => /\/admin\/apriori\/\d+/.test(a.getAttribute('href') || ''));
    return hit ? hit.href : null;
  }).catch(() => null);
  if (detailApriori) {
    await page.goto(detailApriori, { waitUntil: 'domcontentloaded', timeout: 60000 });
    await shot(page, '08-admin-apriori-detail.png');
  }

  console.log('DONE admin shots', OUT);
} catch (e) {
  console.error('ERROR', e);
  process.exitCode = 1;
} finally {
  await browser.close();
}
