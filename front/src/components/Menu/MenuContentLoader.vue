<script setup>
defineProps({
  message: {
    type: String,
    default: 'در حال آماده‌سازی منو...'
  }
})
</script>

<template>
  <div class="menu-loader" role="status" aria-live="polite" aria-busy="true">
    <div class="menu-loader__card">
      <div class="menu-loader__visual">
        <span class="menu-loader__ring menu-loader__ring--outer" aria-hidden="true" />
        <span class="menu-loader__ring menu-loader__ring--inner" aria-hidden="true" />
        <img
          src="@/assets/images/template/ayt-coffee.svg"
          alt=""
          class="menu-loader__icon"
          width="85"
          height="65"
        />
      </div>
      <p class="menu-loader__text">{{ message }}</p>
      <div class="menu-loader__dots" aria-hidden="true">
        <span />
        <span />
        <span />
      </div>
    </div>
  </div>
</template>

<style scoped>
.menu-loader {
  display: flex;
  justify-content: center;
  padding: 2.5rem 1rem 3rem;
  min-height: 280px;
  animation: menu-loader-fade-in 0.35s ease-out;
}

.menu-loader__card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 2rem 2.25rem;
  border-radius: 1.5rem;
  background: linear-gradient(145deg, #ffffff 0%, #f0f2e8 55%, #e8ecd8 100%);
  border: 1px solid rgba(146, 156, 107, 0.25);
  box-shadow:
    0 12px 40px rgba(146, 156, 107, 0.12),
    0 2px 8px rgba(0, 0, 0, 0.04);
  max-width: 320px;
  width: 100%;
}

.menu-loader__visual {
  position: relative;
  width: 120px;
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.menu-loader__ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 2px solid transparent;
}

.menu-loader__ring--outer {
  border-top-color: #929c6b;
  border-right-color: rgba(146, 156, 107, 0.35);
  animation: menu-loader-spin 1.1s linear infinite;
}

.menu-loader__ring--inner {
  inset: 14px;
  border-bottom-color: #d7debd;
  border-left-color: rgba(215, 222, 189, 0.6);
  animation: menu-loader-spin-reverse 0.85s linear infinite;
}

.menu-loader__icon {
  position: relative;
  z-index: 1;
  animation: menu-loader-float 2s ease-in-out infinite;
  filter: drop-shadow(0 4px 10px rgba(146, 156, 107, 0.2));
}

.menu-loader__text {
  margin: 0;
  text-align: center;
  font-size: 1.05rem;
  font-weight: 600;
  color: #4a5238;
  line-height: 1.6;
}

.menu-loader__dots {
  display: flex;
  gap: 0.45rem;
  align-items: center;
  justify-content: center;
}

.menu-loader__dots span {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #929c6b;
  animation: menu-loader-bounce 1.2s ease-in-out infinite;
}

.menu-loader__dots span:nth-child(2) {
  animation-delay: 0.15s;
  background-color: #b5bf8f;
}

.menu-loader__dots span:nth-child(3) {
  animation-delay: 0.3s;
  background-color: #d7debd;
}

@keyframes menu-loader-spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes menu-loader-spin-reverse {
  to {
    transform: rotate(-360deg);
  }
}

@keyframes menu-loader-float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-6px);
  }
}

@keyframes menu-loader-bounce {
  0%,
  80%,
  100% {
    transform: scale(0.65);
    opacity: 0.45;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes menu-loader-fade-in {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
