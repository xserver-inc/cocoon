const {defineConfig} = require('@playwright/test');
const base = require('./playwright.config');

const use = {...base.use, trace: 'off', screenshot: 'only-on-failure'};
delete use.channel;

module.exports = defineConfig({
  ...base,
  testMatch: ['search-submit-position.spec.js', 'search-form-wordpress.spec.js'],
  workers: 2,
  reporter: [['list'], ['json', {outputFile: process.env.COCOON_SEARCH_REPORT || 'test-results/search-input-report.json'}]],
  use,
  projects: [
    {name: 'chrome', use: {browserName: 'chromium', channel: 'chrome'}},
    {name: 'edge', use: {browserName: 'chromium', channel: 'msedge'}},
    {name: 'firefox', use: {browserName: 'firefox'}},
    {name: 'webkit', use: {browserName: 'webkit'}}
  ]
});
