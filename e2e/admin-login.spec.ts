import fs from 'node:fs';
import path from 'node:path';
import { test, expect } from '@playwright/test';

const artifactsDir = path.resolve(process.cwd(), 'playwright-artifacts');
fs.mkdirSync(artifactsDir, { recursive: true });

test('admin login succeeds (staging)', async ({ browser }) => {
  const username = process.env.E2E_ADMIN_USER;
  const password = process.env.E2E_ADMIN_PASS;
  if (!username || !password) {
    throw new Error('Set E2E_ADMIN_USER and E2E_ADMIN_PASS to run this test.');
  }

  const runId = `${Date.now()}`;
  const harPath = path.join(artifactsDir, `admin-login-${runId}.har`);
  const videoDir = path.join(artifactsDir, `videos-${runId}`);
  const consolePath = path.join(artifactsDir, `console-${runId}.log`);

  const consoleLines: string[] = [];

  const context = await browser.newContext({
    recordHar: { path: harPath, content: 'embed' },
    recordVideo: { dir: videoDir, size: { width: 1280, height: 720 } },
  });

  const page = await context.newPage();

  page.on('console', (msg) => {
    consoleLines.push(`[${msg.type()}] ${msg.text()}`);
  });
  page.on('pageerror', (err) => {
    consoleLines.push(`[pageerror] ${String(err)}`);
  });

  await page.goto('/admin/login', { waitUntil: 'domcontentloaded' });
  await page.getByRole('textbox', { name: /user name/i }).fill(username);
  await page.getByRole('textbox', { name: /password/i }).fill(password);

  const [nav] = await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.getByRole('button', { name: /login/i }).click(),
  ]);
  expect(nav).toBeTruthy();

  // Success criteria: we should not remain on /admin/login and should reach the admin area.
  await expect(page).not.toHaveURL(/\/admin\/login/);
  await expect(page).toHaveURL(/\/admin(\/|$)/);

  // Persist console output for debugging.
  fs.writeFileSync(consolePath, consoleLines.join('\n') + '\n', 'utf8');

  await context.close();
});


