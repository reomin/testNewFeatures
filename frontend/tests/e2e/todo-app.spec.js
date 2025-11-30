import { test, expect } from '@playwright/test';

test.describe('Todo App E2E Tests', () => {
  test.beforeEach(async ({ page }) => {
    // ホームページに移動
    await page.goto('/');
    // ページがロードされるまで待機
    await page.waitForLoadState('networkidle');
  });

  test('should display home page correctly', async ({ page }) => {
    // ページタイトルを確認
    await expect(page.locator('h1')).toContainText('This is Todo App');

    // フォームが表示されていることを確認
    await expect(page.locator('input[name="title"]')).toBeVisible();
    await expect(page.locator('input[name="description"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
  });

  test('should navigate to todos list page', async ({ page }) => {
    // Todo一覧ページに移動（ハッシュベースルーティング）
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');

    // Todo一覧ページの要素を確認
    await expect(page.locator('h1')).toContainText('Todo一覧');
    await expect(page.locator('a')).toContainText('ホームに戻る');
  });

  test('should add a new todo', async ({ page }) => {
    const todoTitle = 'テストTodo';
    const todoDescription = 'テスト用のTodoアイテム';

    // フォームに入力
    await page.fill('input[name="title"]', todoTitle);
    await page.fill('input[name="description"]', todoDescription);

    // フォームを送信
    await page.click('button[type="submit"]');

    // リダイレクトされることを確認（PHPのheader locationによる）
    await page.waitForURL('http://localhost:5173/');
  });

  test('should display todos in the list', async ({ page }) => {
    // Todo一覧ページに移動
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');

    // Todoが表示されるまで待機（APIからのデータ取得）
    await page.waitForSelector('[data-testid="todo-item"], p:has-text("Todoがありません")', {
      timeout: 10000
    });

    // Todoがあるかまたはメッセージがあるかをチェック
    const todoExists = await page.locator('[data-testid="todo-item"]').count() > 0;
    const noTodoMessage = await page.locator('p:has-text("Todoがありません")').isVisible();

    expect(todoExists || noTodoMessage).toBeTruthy();
  });

  test('should navigate to todo detail page', async ({ page }) => {
    // Todo一覧ページに移動
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');

    // Todoアイテムがある場合のみテスト実行
    const todoCount = await page.locator('h3').count();

    if (todoCount > 0) {
      // 最初の詳細ボタンをクリック
      await page.locator('button:has-text("詳細")').first().click();

      // 詳細ページの要素を確認
      await expect(page.locator('button:has-text("← 戻る")')).toBeVisible();
    }
  });

  test('should delete a todo', async ({ page }) => {
    // Todo一覧ページに移動
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');

    // Todoアイテムがある場合のみテスト実行
    const todoCount = await page.locator('h3').count();

    if (todoCount > 0) {
      // 削除確認ダイアログを自動で承認
      page.on('dialog', dialog => {
        expect(dialog.message()).toContain('本当に削除しますか？');
        dialog.accept();
      });

      // 削除ボタンをクリック
      await page.locator('button:has-text("削除")').first().click();

      // ページがリロードされることを確認
      await page.waitForLoadState('networkidle');
    }
  });

  test('should search todos using Elasticsearch', async ({ page }) => {
    // Todo一覧ページに移動
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');

    // 検索フォームが表示されることを確認
    await expect(page.locator('input[placeholder="Todoを検索..."]')).toBeVisible();
    await expect(page.locator('button:has-text("検索")')).toBeVisible();

    // 検索クエリを入力
    await page.fill('input[placeholder="Todoを検索..."]', 'テスト');

    // 検索ボタンをクリック
    await page.click('button:has-text("検索")');

    // 検索結果が表示されるまで待機
    await page.waitForTimeout(2000);

    // クリアボタンが表示されることを確認
    await expect(page.locator('button:has-text("クリア")')).toBeVisible();

    // 検索結果情報が表示されることを確認
    await expect(page.locator('text=検索結果:')).toBeVisible();
  });
});