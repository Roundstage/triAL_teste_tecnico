import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authService, type AuthUser } from 'src/services/auth';

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('auth_token'));
  const user = ref<AuthUser | null>(null);

  const isAuthenticated = computed(() => !!token.value);

  async function login(email: string, password: string): Promise<void> {
    const response = await authService.login({ email, senha: password });
    token.value = response.access_token;
    localStorage.setItem('auth_token', response.access_token);
    user.value = await authService.me();
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

  async function fetchUser(): Promise<void> {
    if (!token.value) return;
    user.value = await authService.me();
  }

  return { token, user, isAuthenticated, login, logout, fetchUser };
});
