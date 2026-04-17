<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated class="bg-primary">
      <q-toolbar>
        <q-toolbar-title class="text-weight-bold">triAL</q-toolbar-title>

        <q-tabs align="left" class="q-mr-auto">
          <q-route-tab to="/home" label="Home" icon="home" />
        </q-tabs>

        <q-btn
          flat
          icon="logout"
          label="Sair"
          :loading="loggingOut"
          @click="onLogout"
        />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from 'src/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const loggingOut = ref(false);

async function onLogout() {
  loggingOut.value = true;
  await authStore.logout();
  await router.push('/login');
}
</script>
