import { test, expect } from '@playwright/test';
import { mockMe, mockLogout, setAuthenticated, clearAuth, MOCK_USER } from './helpers';

test.describe('Home Page', () => {
  test('redireciona para /login sem autenticação', async ({ page }) => {
    await clearAuth(page);
    await page.goto('/#/home');
    await expect(page).toHaveURL(/#\/login/);
  });

  test('exibe dados do usuário autenticado', async ({ page }) => {
    await setAuthenticated(page);
    await mockMe(page);
    await page.goto('/#/home');

    await expect(page.getByText(MOCK_USER.nome)).toBeVisible();
    await expect(page.getByText(MOCK_USER.email)).toBeVisible();
    await expect(page.getByText(MOCK_USER.telefone)).toBeVisible();
  });

  test('exibe data de expiração da conta', async ({ page }) => {
    await setAuthenticated(page);
    await mockMe(page);
    await page.goto('/#/home');

    await expect(page.getByText('Conta expira em')).toBeVisible();
    // data_expiracao: 2026-04-24 → exibida como dias restantes
    await expect(page.locator('.text-h4')).toBeVisible();
  });

  test('navbar exibe título triAL e link Home', async ({ page }) => {
    await setAuthenticated(page);
    await mockMe(page);
    await page.goto('/#/home');

    await expect(page.getByText('triAL')).toBeVisible();
    await expect(page.getByRole('tab', { name: 'Home' })).toBeVisible();
  });

  test('logout redireciona para /login', async ({ page }) => {
    await setAuthenticated(page);
    await mockMe(page);
    await mockLogout(page);
    await page.goto('/#/home');

    await page.getByRole('button', { name: 'Sair' }).click();

    await expect(page).toHaveURL(/#\/login/);
  });

  test('rota raiz / redireciona para /login', async ({ page }) => {
    await clearAuth(page);
    await page.goto('/');
    await expect(page).toHaveURL(/#\/login/);
  });

  test('rota desconhecida cai na página de erro 404', async ({ page }) => {
    await page.goto('/#/rota-inexistente');
    await expect(page.getByText('404')).toBeVisible();
  });
});
