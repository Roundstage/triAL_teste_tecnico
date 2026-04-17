<template>
  <div class="login-page">
    <img
      src="src/assets/gabriel-santos-GBVDilE8yvI-unsplash.jpg"
      class="login-page__bg"
      alt=""
      aria-hidden="true"
    />

    <div class="login-page__hero">
      <div class="login-page__overlay" />
      <div class="login-page__brand">
        <div class="text-h3 text-white text-weight-bold">triAL</div>
        <div class="text-subtitle1 text-white opacity-80">Warehouse Management System</div>
      </div>
    </div>

    <div class="login-page__form-side">
      <q-card flat class="login-page__card">
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
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from 'src/stores/auth';

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
    await router.push('/app');
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

<style lang="scss" scoped>
.login-page {
  position: relative;
  display: flex;
  height: 100vh;
  overflow: hidden;

  &__bg {
    position: fixed;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center bottom;
    z-index: 0;
  }

  &__hero {
    position: relative;
    flex: 1 1 60%;
    display: flex;
    align-items: flex-end;
    padding: 48px;
    z-index: 1;
  }

  &__overlay {
    position: absolute;
    inset: 0;
    background: rgba($primary, 0.35);
  }

  &__brand {
    position: relative;
    z-index: 1;
  }

  &__form-side {
    position: relative;
    flex: 0 0 420px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    padding: 48px 40px;
    z-index: 1;
  }

  &__card {
    width: 100%;
    max-width: 360px;
  }
}

@media (max-width: 768px) {
  .login-page {
    &__bg {
      filter: blur(6px) brightness(0.6);
      transform: scale(1.1);
    }

    &__hero {
      display: none;
    }

    &__form-side {
      flex: 1;
      background: transparent;
      padding: 24px 32px;
    }

    &__card {
      background: rgba(255, 255, 255, 0.92);
      box-shadow: 0 4px 32px rgba(0, 0, 0, 0.18);
      border-radius: 16px;
    }
  }
}
</style>
