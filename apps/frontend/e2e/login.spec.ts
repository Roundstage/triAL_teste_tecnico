import { test, expect } from '@playwright/test';
import { mockLogin, mockMe, setAuthenticated, clearAuth } from './helpers';

test.describe('Login Page', () => {
  test.beforeEach(async ({ page }) => {
    await clearAuth(page);
  });

  test('renderiza o formulário corretamente', async ({ page }) => {
    await page.goto('/#/login');

    await expect(page.getByText('Bem-vindo')).toBeVisible();
    await expect(page.getByText('Acesse sua conta para continuar')).toBeVisible();
    await expect(page.getByLabel('E-mail')).toBeVisible();
    await expect(page.getByLabel('Senha')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Entrar' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Criar conta' })).toBeVisible();
  });

  test('link "Criar conta" navega para /cadastro', async ({ page }) => {
    await page.goto('/#/login');
    await page.getByRole('link', { name: 'Criar conta' }).click();
    await expect(page).toHaveURL(/#\/cadastro/);
  });

  test('exibe erro com credenciais inválidas (401)', async ({ page }) => {
    await mockLogin(page, 401);
    await page.goto('/#/login');

    await page.getByLabel('E-mail').fill('wrong@email.com');
    await page.getByLabel('Senha').fill('senhaerrada');
    await page.getByRole('button', { name: 'Entrar' }).click();

    await expect(page.getByText('E-mail ou senha inválidos.')).toBeVisible();
  });

  test('exibe erro de rate limit (429)', async ({ page }) => {
    await mockLogin(page, 429);
    await page.goto('/#/login');

    await page.getByLabel('E-mail').fill('any@email.com');
    await page.getByLabel('Senha').fill('qualquersenha');
    await page.getByRole('button', { name: 'Entrar' }).click();

    await expect(page.getByText('Muitas tentativas. Aguarde um momento.')).toBeVisible();
  });

  test('exibe erro genérico em falha de servidor (500)', async ({ page }) => {
    await mockLogin(page, 500);
    await page.goto('/#/login');

    await page.getByLabel('E-mail').fill('any@email.com');
    await page.getByLabel('Senha').fill('qualquersenha');
    await page.getByRole('button', { name: 'Entrar' }).click();

    await expect(page.getByText('Erro ao conectar com o servidor.')).toBeVisible();
  });

  test('login com sucesso redireciona para /home', async ({ page }) => {
    await mockLogin(page, 200);
    await mockMe(page);
    await page.goto('/#/login');

    await page.getByLabel('E-mail').fill('allison@teste.com');
    await page.getByLabel('Senha').fill('senha123');
    await page.getByRole('button', { name: 'Entrar' }).click();

    await expect(page).toHaveURL(/#\/home/);
  });

  test('usuário já autenticado é redirecionado para /home', async ({ page }) => {
    await setAuthenticated(page);
    await page.goto('/#/login');
    await expect(page).toHaveURL(/#\/home/);
  });

  test('toggle de visibilidade da senha funciona', async ({ page }) => {
    await page.goto('/#/login');

    const senhaInput = page.getByLabel('Senha');
    await expect(senhaInput).toHaveAttribute('type', 'password');

    await page.locator('.cursor-pointer').click();
    await expect(senhaInput).toHaveAttribute('type', 'text');

    await page.locator('.cursor-pointer').click();
    await expect(senhaInput).toHaveAttribute('type', 'password');
  });
});
