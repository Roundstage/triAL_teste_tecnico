import { api } from 'src/boot/axios';

export interface LoginCredentials {
  email: string;
  senha: string;
}

export interface AuthUser {
  id: number;
  name: string;
  email: string;
}

export interface AuthResponse {
  access_token: string;
  token_type: string;
  expires_in: number;
}

export const authService = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const { data } = await api.post<AuthResponse>('/auth/login', credentials);
    return data;
  },

  async logout(): Promise<void> {
    await api.post('/auth/logout');
  },

  async me(): Promise<AuthUser> {
    const { data } = await api.get<AuthUser>('/auth/me');
    return data;
  },
};
