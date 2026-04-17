<template>
  <q-page class="flex flex-center bg-grey-1">
    <q-card class="user-card" flat bordered>
      <q-card-section class="row items-center q-gutter-md">
        <q-avatar size="64px" color="primary" text-color="white" icon="person" />
        <div>
          <div class="text-h6 text-weight-bold">{{ usuario?.nome ?? '—' }}</div>
          <div class="text-body2 text-grey-7">{{ usuario?.email ?? '—' }}</div>
          <div class="text-body2 text-grey-7">{{ usuario?.telefone ?? '—' }}</div>
        </div>
      </q-card-section>

      <q-separator />

      <q-card-section class="text-center">
        <template v-if="usuario?.data_expiracao">
          <div class="text-caption text-grey-6 q-mb-xs">Conta expira em</div>
          <div class="text-h4 text-weight-bold" :class="corCronometro">
            {{ cronometro }}
          </div>
          <div class="text-caption text-grey-5 q-mt-xs">
            {{ dataExpiracaoFormatada }}
          </div>
        </template>
        <template v-else>
          <div class="text-body2 text-grey-6">Sem data de expiração definida</div>
        </template>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from 'src/stores/auth';

const authStore = useAuthStore();
const usuario = computed(() => authStore.user);

const agora = ref(Date.now());

const expTimestamp = computed(() => {
  if (!usuario.value?.data_expiracao) return null;
  return new Date(usuario.value.data_expiracao).getTime();
});

const msRestantes = computed(() => {
  if (!expTimestamp.value) return 0;
  return Math.max(0, expTimestamp.value - agora.value);
});

const cronometro = computed(() => {
  const total = Math.floor(msRestantes.value / 1000);
  const d = Math.floor(total / 86400);
  const h = Math.floor((total % 86400) / 3600);
  const m = Math.floor((total % 3600) / 60);
  const s = total % 60;
  const pad = (n: number) => String(n).padStart(2, '0');
  if (d > 0) return `${d}d ${pad(h)}h ${pad(m)}m`;
  return `${pad(h)}:${pad(m)}:${pad(s)}`;
});

const dataExpiracaoFormatada = computed(() => {
  if (!usuario.value?.data_expiracao) return '';
  return new Date(usuario.value.data_expiracao).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  });
});

const corCronometro = computed(() => {
  const dias = msRestantes.value / 86400000;
  if (dias <= 1) return 'text-negative';
  if (dias <= 7) return 'text-warning';
  return 'text-positive';
});

let intervalo: ReturnType<typeof setInterval> | null = null;

onMounted(async () => {
  await authStore.fetchMe();
  intervalo = setInterval(() => {
    agora.value = Date.now();
  }, 1000);
});

onUnmounted(() => {
  if (intervalo) clearInterval(intervalo);
});
</script>

<style scoped>
.user-card {
  width: 100%;
  max-width: 680px;
}
</style>
