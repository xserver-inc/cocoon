const {defineConfig} = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/e2e',
  timeout: 30000,
  expect: {timeout: 5000},
  fullyParallel: false,
  workers: 1,
  reporter: [['list']],
  use: {
    channel: 'msedge',
    headless: true,
    ignoreHTTPSErrors: true,
    trace: 'retain-on-failure'
  }
});
