<template>
  <AuthLayout>
    <q-card-section class="q-pb-none">
      <div class="text-h5 text-weight-bold text-dark q-mb-xs">Criar conta</div>
      <div class="text-body2 text-grey-7">Preencha os dados para se cadastrar</div>
    </q-card-section>

    <q-card-section>
      <q-form @submit.prevent="onSubmit" class="q-gutter-y-md">
        <q-input
          v-model="form.nome"
          label="Nome completo"
          outlined
          :rules="[(v) => !!v || 'Campo obrigatório']"
        >
          <template #prepend><q-icon name="person_outline" /></template>
        </q-input>

        <q-input
          v-model="form.email"
          type="email"
          label="E-mail"
          outlined
          :rules="[(v) => !!v || 'Campo obrigatório']"
        >
          <template #prepend><q-icon name="mail_outline" /></template>
        </q-input>

        <q-input
          v-model="form.telefone"
          label="Telefone"
          outlined
          mask="(##) #####-####"
          unmasked-value
          :rules="[(v) => !!v || 'Campo obrigatório']"
        >
          <template #prepend><q-icon name="phone" /></template>
        </q-input>

        <q-input
          v-model="form.data_nascimento"
          label="Data de nascimento"
          outlined
          mask="##/##/####"
          placeholder="DD/MM/AAAA"
          :rules="[(v) => !!v || 'Campo obrigatório']"
        >
          <template #prepend><q-icon name="cake" /></template>
        </q-input>

        <q-input
          v-model="form.senha"
          :type="showSenha ? 'text' : 'password'"
          label="Senha"
          outlined
          :rules="[(v) => !!v || 'Campo obrigatório', (v) => v.length >= 6 || 'Mínimo 6 caracteres']"
        >
          <template #prepend><q-icon name="lock_outline" /></template>
          <template #append>
            <q-icon
              :name="showSenha ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="showSenha = !showSenha"
            />
          </template>
        </q-input>

        <q-input
          v-model="form.senha_confirmation"
          :type="showSenhaConf ? 'text' : 'password'"
          label="Confirmar senha"
          outlined
          :rules="[(v) => !!v || 'Campo obrigatório', (v) => v === form.senha || 'Senhas não coincidem']"
        >
          <template #prepend><q-icon name="lock_outline" /></template>
          <template #append>
            <q-icon
              :name="showSenhaConf ? 'visibility_off' : 'visibility'"
              class="cursor-pointer"
              @click="showSenhaConf = !showSenhaConf"
            />
          </template>
        </q-input>

        <q-banner v-if="erro" dense rounded class="text-white bg-negative">
          {{ erro }}
        </q-banner>

        <q-btn
          type="submit"
          label="Cadastrar"
          color="primary"
          class="full-width q-mt-sm"
          size="lg"
          unelevated
          :loading="loading"
        />

        <div class="text-center text-body2 text-grey-7">
          Já tem uma conta?
          <router-link to="/login" class="text-primary text-weight-medium">Entrar</router-link>
        </div>
      </q-form>
    </q-card-section>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from 'src/stores/auth';
import AuthLayout from 'src/components/AuthLayout.vue';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  nome: '',
  email: '',
  telefone: '',
  data_nascimento: '',
  senha: '',
  senha_confirmation: '',
});

const showSenha = ref(false);
const showSenhaConf = ref(false);
const loading = ref(false);
const erro = ref('');

function formatarData(ddmmaaaa: string): string {
  const [dia, mes, ano] = ddmmaaaa.split('/');
  return `${ano}-${mes}-${dia}`;
}

async function onSubmit() {
  erro.value = '';
  loading.value = true;
  try {
    await authStore.register({
      ...form,
      data_nascimento: formatarData(form.data_nascimento),
    });
    await router.push('/home');
  } catch (e: unknown) {
    const err = e as { response?: { status?: number; data?: { message?: string; errors?: Record<string, string[]> } } };
    if (err.response?.status === 422) {
      const errors = err.response.data?.errors;
      erro.value = errors ? Object.values(errors).flat()[0] ?? 'Dados inválidos.' : 'Dados inválidos.';
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
