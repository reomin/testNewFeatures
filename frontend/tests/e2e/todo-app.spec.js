import { test, expect } from '@playwright/test';

test.describe('Todo App E2E Tests', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('networkidle');
  });

  test('should display home page correctly', async ({ page }) => {
    await expect(page.locator('h1')).toContainText('This is Todo App');
    await expect(page.locator('input[name="title"]')).toBeVisible();
    await expect(page.locator('input[name="description"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
  });

  test('should navigate to todos list page', async ({ page }) => {
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('h1')).toContainText('Todo一覧');
    await expect(page.locator('a')).toContainText('ホームに戻る');
  });

  test('should add a new todo', async ({ page }) => {
    await page.fill('input[name="title"]', 'テストTodo');
    await page.fill('input[name="description"]', 'テスト用のTodoアイテム');
    await page.click('button[type="submit"]');
    await page.waitForURL('http://localhost:5173/');
  });

  test('should display todos in the list', async ({ page }) => {
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');
    await page.waitForSelector('[data-testid="todo-item"], p:has-text("Todoがありません")', { timeout: 10000 });
    
    const todoExists = await page.locator('[data-testid="todo-item"]').count() > 0;
    const noTodoMessage = await page.locator('p:has-text("Todoがありません")').isVisible();
    expect(todoExists || noTodoMessage).toBeTruthy();
  });

  test('should navigate to todo detail page', async ({ page }) => {
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');
    
    const todoCount = await page.locator('h3').count();
    if (todoCount > 0) {
      await page.locator('button:has-text("詳細")').first().click();
      await expect(page.locator('button:has-text("← 戻る")')).toBeVisible();
    }
  });

  test('should delete a todo', async ({ page }) => {
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');
    
    const todoCount = await page.locator('h3').count();
    if (todoCount > 0) {
      page.on('dialog', dialog => dialog.accept());
      await page.locator('button:has-text("削除")').first().click();
      await page.waitForLoadState('networkidle');
    }
  });

  test('should search todos using Elasticsearch', async ({ page }) => {
    await page.goto('/#todos');
    await page.waitForLoadState('networkidle');
    
    await expect(page.locator('input[placeholder="Todoを検索..."]')).toBeVisible();
    await expect(page.locator('button:has-text("検索")')).toBeVisible();
    
    await page.fill('input[placeholder="Todoを検索..."]', 'テスト');
    await page.click('button:has-text("検索")');
    await page.waitForTimeout(2000);
    
    await expect(page.locator('button:has-text("クリア")')).toBeVisible();
    await expect(page.locator('text=検索結果:')).toBeVisible();
  });
});