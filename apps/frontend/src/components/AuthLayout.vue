<template>
  <div class="auth-layout">
    <img
      src="src/assets/gabriel-santos-GBVDilE8yvI-unsplash.jpg"
      class="auth-layout__bg"
      alt=""
      aria-hidden="true"
    />

    <div class="auth-layout__hero">
      <div class="auth-layout__overlay" />
      <div class="auth-layout__brand">
        <div class="text-h3 text-white text-weight-bold">triAL</div>
        <div class="text-subtitle1 text-white opacity-80">Warehouse Management System</div>
      </div>
    </div>

    <div class="auth-layout__form-side">
      <q-card flat class="auth-layout__card">
        <slot />
      </q-card>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.auth-layout {
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
    overflow-y: auto;
    z-index: 1;
  }

  &__card {
    width: 100%;
    max-width: 360px;
  }
}

@media (max-width: 768px) {
  .auth-layout {
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
      align-items: flex-start;
      padding-top: 40px;
    }

    &__card {
      background: rgba(255, 255, 255, 0.92);
      box-shadow: 0 4px 32px rgba(0, 0, 0, 0.18);
      border-radius: 16px;
    }
  }
}
</style>
