import { api } from 'src/boot/axios';

export interface LoginCredentials {
  email: string;
  senha: string;
}

export interface RegisterCredentials {
  nome: string;
  email: string;
  senha: string;
  senha_confirmation: string;
  telefone: string;
  data_nascimento: string;
}

export interface AuthUser {
  id: number;
  nome: string;
  email: string;
  telefone: string;
  data_nascimento: string;
  status: string;
}

export interface AuthResponse {
  token: string;
  usuario: AuthUser;
}

export const authService = {
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const { data } = await api.post<AuthResponse>('/auth/login', credentials);
    return data;
  },

  async register(credentials: RegisterCredentials): Promise<AuthResponse> {
    const { data } = await api.post<AuthResponse>('/auth/register', credentials);
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
