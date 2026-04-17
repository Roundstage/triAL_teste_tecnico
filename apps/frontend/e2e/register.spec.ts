import { test, expect } from '@playwright/test';
import { mockRegister, mockMe, setAuthenticated, clearAuth } from './helpers';

test.describe('Register Page', () => {
  test.beforeEach(async ({ page }) => {
    await clearAuth(page);
  });

  test('renderiza todos os campos do formulário', async ({ page }) => {
    await page.goto('/#/cadastro');

    await expect(page.getByText('Criar conta')).toBeVisible();
    await expect(page.getByLabel('Nome completo')).toBeVisible();
    await expect(page.getByLabel('E-mail')).toBeVisible();
    await expect(page.getByLabel('Telefone')).toBeVisible();
    await expect(page.getByLabel('Data de nascimento')).toBeVisible();
    await expect(page.getByLabel('Senha', { exact: true })).toBeVisible();
    await expect(page.getByLabel('Confirmar senha')).toBeVisible();
    await expect(page.getByRole('button', { name: 'Cadastrar' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Entrar' })).toBeVisible();
  });

  test('link "Entrar" navega para /login', async ({ page }) => {
    await page.goto('/#/cadastro');
    await page.getByRole('link', { name: 'Entrar' }).click();
    await expect(page).toHaveURL(/#\/login/);
  });

  test('cadastro com sucesso redireciona para /home', async ({ page }) => {
    await mockRegister(page, 200);
    await mockMe(page);
    await page.goto('/#/cadastro');

    await page.getByLabel('Nome completo').fill('Allison Martins');
    await page.getByLabel('E-mail').fill('allison@teste.com');
    await page.getByLabel('Telefone').fill('11999999999');
    await page.getByLabel('Data de nascimento').fill('10/05/1998');
    await page.getByLabel('Senha', { exact: true }).fill('senha123');
    await page.getByLabel('Confirmar senha').fill('senha123');
    await page.getByRole('button', { name: 'Cadastrar' }).click();

    await expect(page).toHaveURL(/#\/home/);
  });

  test('exibe erro de validação do backend (422)', async ({ page }) => {
    await mockRegister(page, 422, { email: ['O campo e-mail já está em uso.'] });
    await page.goto('/#/cadastro');

    await page.getByLabel('Nome completo').fill('Allison Martins');
    await page.getByLabel('E-mail').fill('existente@teste.com');
    await page.getByLabel('Telefone').fill('11999999999');
    await page.getByLabel('Data de nascimento').fill('10/05/1998');
    await page.getByLabel('Senha', { exact: true }).fill('senha123');
    await page.getByLabel('Confirmar senha').fill('senha123');
    await page.getByRole('button', { name: 'Cadastrar' }).click();

    await expect(page.getByText('O campo e-mail já está em uso.')).toBeVisible();
  });

  test('exibe erro de rate limit (429)', async ({ page }) => {
    await mockRegister(page, 429);
    await page.goto('/#/cadastro');

    await page.getByLabel('Nome completo').fill('Allison Martins');
    await page.getByLabel('E-mail').fill('allison@teste.com');
    await page.getByLabel('Telefone').fill('11999999999');
    await page.getByLabel('Data de nascimento').fill('10/05/1998');
    await page.getByLabel('Senha', { exact: true }).fill('senha123');
    await page.getByLabel('Confirmar senha').fill('senha123');
    await page.getByRole('button', { name: 'Cadastrar' }).click();

    await expect(page.getByText('Muitas tentativas. Aguarde um momento.')).toBeVisible();
  });

  test('usuário já autenticado é redirecionado para /home', async ({ page }) => {
    await setAuthenticated(page);
    await page.goto('/#/cadastro');
    await expect(page).toHaveURL(/#\/home/);
  });
});
