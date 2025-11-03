const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

// スクリーンショット保存ディレクトリを作成
const screenshotDir = path.join(__dirname, 'screenshots');
if (!fs.existsSync(screenshotDir)) {
  fs.mkdirSync(screenshotDir, { recursive: true });
}

async function captureScreenshots() {
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  const page = await browser.newPage();

  // ビューポートを設定
  await page.setViewport({ width: 1280, height: 720 });

  try {
    // ページのリスト
    const pages = [
      { name: 'home', url: 'http://localhost:5173/' },
      { name: 'todos', url: 'http://localhost:5173/#todos' },
    ];

    for (const pageInfo of pages) {
      console.log(`Capturing ${pageInfo.name}...`);

      await page.goto(pageInfo.url, {
        waitUntil: 'networkidle2',
        timeout: 30000
      });

      // ページが完全に読み込まれるまで少し待つ
      await new Promise(resolve => setTimeout(resolve, 2000));

      // スクリーンショットを撮影
      await page.screenshot({
        path: path.join(screenshotDir, `${pageInfo.name}.png`),
        fullPage: true
      });

      console.log(`✓ ${pageInfo.name}.png saved`);
    }

  } catch (error) {
    console.error('Error capturing screenshots:', error);
  } finally {
    await browser.close();
  }
}

// スクリプト実行
captureScreenshots().then(() => {
  console.log('All screenshots captured successfully!');
}).catch(error => {
  console.error('Failed to capture screenshots:', error);
  process.exit(1);
});