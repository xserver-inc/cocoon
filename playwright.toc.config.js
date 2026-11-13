const {defineConfig} = require('@playwright/test');
const base = require('./playwright.config');

const use = {...base.use, trace: 'off', screenshot: 'only-on-failure'};
delete use.channel;

module.exports = defineConfig({
  ...base,
  testMatch: ['toc-scroll-wordpress.spec.js'],
  globalSetup: require.resolve('./tests/e2e/toc-scroll-wordpress-setup'),
  workers: 2,
  reporter: [['list'], ['json', {outputFile: process.env.COCOON_TOC_REPORT || 'test-results/toc-scroll-report.json'}]],
  use,
  projects: [
    {name: 'edge', use: {browserName: 'chromium', channel: 'msedge'}},
    {name: 'firefox', use: {browserName: 'firefox', launchOptions: {executablePath: process.env.COCOON_TOC_FIREFOX_PATH || undefined}}}
  ]
});
