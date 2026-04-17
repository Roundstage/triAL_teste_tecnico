import { Page } from '@playwright/test';

const API_URL = 'http://localhost:8000/api';

export const MOCK_TOKEN =
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.' +
  btoa(JSON.stringify({ sub: 1, iat: 1700000000, exp: 9999999999 }))
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=+$/, '') +
  '.mock_signature';

export const MOCK_USER = {
  id: 1,
  nome: 'Allison Martins',
  email: 'allison@teste.com',
  telefone: '11999999999',
  data_nascimento: '1998-05-10',
  data_expiracao: '2026-04-24',
  status: 'ativo',
};

export const MOCK_AUTH_RESPONSE = {
  token: MOCK_TOKEN,
  usuario: MOCK_USER,
};

export async function mockLogin(page: Page, status = 200) {
  await page.route(`${API_URL}/auth/login`, (route) => {
    if (status === 200) {
      route.fulfill({ status: 200, json: MOCK_AUTH_RESPONSE });
    } else {
      route.fulfill({ status, json: { message: 'Credenciais inválidas.' } });
    }
  });
}

export async function mockMe(page: Page) {
  await page.route(`${API_URL}/auth/me`, (route) => {
    route.fulfill({ status: 200, json: MOCK_USER });
  });
}

export async function mockLogout(page: Page) {
  await page.route(`${API_URL}/auth/logout`, (route) => {
    route.fulfill({ status: 200, json: { message: 'Sessão encerrada.' } });
  });
}

export async function mockRegister(page: Page, status = 200, errors?: Record<string, string[]>) {
  await page.route(`${API_URL}/auth/register`, (route) => {
    if (status === 200) {
      route.fulfill({ status: 200, json: MOCK_AUTH_RESPONSE });
    } else if (status === 422) {
      route.fulfill({ status: 422, json: { message: 'Dados inválidos.', errors } });
    } else {
      route.fulfill({ status, json: { message: 'Erro.' } });
    }
  });
}

export async function setAuthenticated(page: Page) {
  await page.addInitScript((token) => {
    localStorage.setItem('auth_token', token);
  }, MOCK_TOKEN);
}

export async function clearAuth(page: Page) {
  await page.addInitScript(() => {
    localStorage.removeItem('auth_token');
  });
}
