<template>
  <div class="login-page">
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
          <q-form @submit.prevent="onSubmit" class="q-gutter-md">
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

const email = ref('');
const senha = ref('');
const showSenha = ref(false);
const loading = ref(false);

async function onSubmit() {
  loading.value = true;
  // TODO: integrar com AuthService
  await new Promise((r) => setTimeout(r, 800));
  loading.value = false;
}
</script>

<style lang="scss" scoped>
.login-page {
  display: flex;
  min-height: 100vh;

  &__hero {
    position: relative;
    flex: 1 1 60%;
    background-image: url('src/assets/gabriel-santos-GBVDilE8yvI-unsplash.jpg');
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: flex-end;
    padding: 48px;
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
    flex: 0 0 420px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    padding: 48px 40px;
  }

  &__card {
    width: 100%;
    max-width: 360px;
  }
}

@media (max-width: 768px) {
  .login-page {
    flex-direction: column;

    &__hero {
      flex: 0 0 220px;
      padding: 32px 24px;
    }

    &__form-side {
      flex: 1;
      padding: 32px 24px;
    }
  }
}
</style>
