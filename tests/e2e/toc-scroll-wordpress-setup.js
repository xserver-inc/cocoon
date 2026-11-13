const fs = require('fs');
const path = require('path');
const {execFile} = require('child_process');
const {promisify} = require('util');

module.exports = async () => {
  const baseUrl = process.env.COCOON_TOC_BASE_URL;
  if (!baseUrl) {
    return;
  }
  const url = new URL(baseUrl);
  if (url.protocol !== 'http:' || !['localhost', '127.0.0.1'].includes(url.hostname) ||
    !['8085', '8090'].includes(url.port) || url.pathname !== '/' || url.username || url.password) {
    throw new Error('目次の実機確認先にはプロジェクトのDockerローカル環境を指定してください。');
  }
  const scratch = path.resolve(__dirname, '../../scratch');
  fs.mkdirSync(scratch, {recursive: true});
  const directory = fs.mkdtempSync(path.join(scratch, 'toc-scroll-'));
  const cleanup = () => {
    // プロジェクト内の今回作成した一時ディレクトリーだけを対象とする削除
    const relative = path.relative(scratch, directory);
    if (!relative.startsWith('toc-scroll-') || relative.includes(path.sep) || path.isAbsolute(relative)) {
      throw new Error('目次テストの一時ファイルの保存先が想定外です。');
    }
    fs.rmSync(directory, {recursive: true, force: true});
  };
  try {
    const project = url.port === '8090' ? 'cocoon-wp70-php84' : 'cocoon-wp68-php83';
    const localDocker = path.join(process.env.LOCALAPPDATA || '', 'Programs/DockerDesktop/resources/bin/docker.exe');
    const docker = process.env.COCOON_TOC_DOCKER || (fs.existsSync(localDocker) ? localDocker : 'docker');
    // ブラウザー起動前の、全プロジェクトで共用する検証HTMLの一度だけの生成
    for (const mode of ['fixed', 'static', 'logged']) {
      const {stdout: html} = await promisify(execFile)(docker, ['exec', `${project}-wordpress-1`, 'php',
        '/var/www/html/wp-content/themes/cocoon/tests/fixtures/toc-scroll-wordpress.php', mode, url.port],
      {encoding: 'utf8', timeout: 180000, maxBuffer: 4 * 1024 * 1024});
      if (!html.includes('id="toc"') || !html.includes('id="toc2"') || !html.includes('wp-child-theme-cocoon-child')) {
        throw new Error('親子テーマと標準目次を使った検証ページを生成できませんでした。');
      }
      fs.writeFileSync(path.join(directory, mode + '.html'), html);
    }
    process.env.COCOON_TOC_FIXTURES = directory;
    return cleanup;
  } catch (error) {
    cleanup();
    throw error;
  }
};
