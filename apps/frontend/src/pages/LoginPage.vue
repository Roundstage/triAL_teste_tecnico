<template>
  <AuthLayout>
    <q-card-section class="q-pb-none">
      <div class="text-h5 text-weight-bold text-dark q-mb-xs">Bem-vindo</div>
      <div class="text-body2 text-grey-7">Acesse sua conta para continuar</div>
    </q-card-section>

    <q-card-section>
      <q-form @submit.prevent="onSubmit" class="q-gutter-y-md">
        <q-input
          v-model="email"
          type="email"
          label="E-mail"
          outlined
          :rules="[(v) => !!v || 'Campo obrigatório']"
        >
          <template #prepend>
            <q-icon name="mail_outline" />
          </template>
        </q-input>

        <q-input
          v-model="senha"
          :type="showSenha ? 'text' : 'password'"
          label="Senha"
          outlined
          :rules="[(v) => !!v || 'Campo obrigatório']"
        >
          <template #prepend>
            <q-icon name="lock_outline" />
          </template>
          <template #append>
            <q-icon
              :name="showSenha ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="showSenha = !showSenha"
            />
          </template>
        </q-input>

        <q-banner v-if="erro" dense rounded class="text-white bg-negative q-mt-sm">
          {{ erro }}
        </q-banner>

        <q-btn
          type="submit"
          label="Entrar"
          color="primary"
          class="full-width q-mt-sm"
          size="lg"
          unelevated
          :loading="loading"
        />

        <div class="text-center text-body2 text-grey-7">
          Não tem uma conta?
          <router-link to="/cadastro" class="text-primary text-weight-medium">Criar conta</router-link>
        </div>
      </q-form>
    </q-card-section>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from 'src/stores/auth';
import AuthLayout from 'src/components/AuthLayout.vue';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const senha = ref('');
const showSenha = ref(false);
const loading = ref(false);
const erro = ref('');

async function onSubmit() {
  erro.value = '';
  loading.value = true;
  try {
    await authStore.login(email.value, senha.value);
    await router.push('/home');
  } catch (e: unknown) {
    const err = e as { response?: { status?: number } };
    if (err.response?.status === 401) {
      erro.value = 'E-mail ou senha inválidos.';
    } else if (err.response?.status === 429) {
      erro.value = 'Muitas tentativas. Aguarde um momento.';
    } else {
      erro.value = 'Erro ao conectar com o servidor.';
    }
  } finally {
    loading.value = false;
  }
}
</script>
