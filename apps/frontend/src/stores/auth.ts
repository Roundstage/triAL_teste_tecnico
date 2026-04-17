import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authService, type AuthUser, type RegisterCredentials } from 'src/services/auth';

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('auth_token'));
  const user = ref<AuthUser | null>(null);

  const isAuthenticated = computed(() => !!token.value);

  async function login(email: string, senha: string): Promise<void> {
    const response = await authService.login({ email, senha });
    token.value = response.token;
    localStorage.setItem('auth_token', response.token);
    user.value = response.usuario;
  }

  async function register(dados: RegisterCredentials): Promise<void> {
    const response = await authService.register(dados);
    token.value = response.token;
    localStorage.setItem('auth_token', response.token);
    user.value = response.usuario;
  }

  async function logout(): Promise<void> {
    try {
      await authService.logout();
    } finally {
      token.value = null;
      user.value = null;
      localStorage.removeItem('auth_token');
    }
  }

  async function fetchMe(): Promise<void> {
    if (!token.value) return;
    user.value = await authService.me();
  }

  return { token, user, isAuthenticated, login, register, logout, fetchMe };
});
