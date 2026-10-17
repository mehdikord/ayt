<script>
export default {
  name: 'Auth'
}
</script>

<template>
  <div class="login-bg">
    <div class="coming-soon">
      <div class="coming-soon__card">
        <div class="coming-soon__visual">
          <span class="coming-soon__glow" aria-hidden="true" />
          <img
            src="@/assets/images/template/auth-logo.svg"
            alt=""
            class="coming-soon__logo"
            width="120"
            height="120"
          />
        </div>

        <div class="coming-soon__badge">به‌زودی</div>

        <h1 class="coming-soon__title">این قسمت به زودی آماده میشه !</h1>

        <p class="coming-soon__subtitle">
          بخش پروفایل و ورود به حساب کاربری در حال آماده‌سازیه.
          <br />
          به‌زودی می‌تونی از امکانات اختصاصی آ‌ی‌ت کافه استفاده کنی.
        </p>

        <router-link :to="{ name: 'index' }" class="coming-soon__link">
          <Button
            label="بازگشت به منو"
            class="ayt-bg-light ayt-border-light"
            rounded
            size="large"
          />
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-bg {
  min-height: 92vh;
  background: url("@/assets/images/template/login-bg.svg");
  background-size: cover;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1.25rem 3rem;
}

.coming-soon {
  width: 100%;
  max-width: 360px;
  animation: coming-soon-fade-in 0.45s ease-out;
}

.coming-soon__card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.1rem;
  padding: 2.25rem 1.75rem 2rem;
  border-radius: 1.75rem;
  background: linear-gradient(160deg, #ffffff 0%, #f4f6ec 52%, #e9eddc 100%);
  border: 1px solid rgba(146, 156, 107, 0.28);
  box-shadow:
    0 16px 48px rgba(146, 156, 107, 0.14),
    0 4px 12px rgba(0, 0, 0, 0.05);
  text-align: center;
}

.coming-soon__visual {
  position: relative;
  width: 130px;
  height: 130px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.coming-soon__glow {
  position: absolute;
  inset: 8px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(215, 222, 189, 0.55) 0%, rgba(215, 222, 189, 0) 72%);
  animation: coming-soon-pulse 2.4s ease-in-out infinite;
}

.coming-soon__logo {
  position: relative;
  z-index: 1;
  animation: coming-soon-float 2.6s ease-in-out infinite;
  filter: drop-shadow(0 6px 14px rgba(146, 156, 107, 0.22));
}

.coming-soon__badge {
  padding: 0.35rem 1rem;
  border-radius: 999px;
  background-color: #929c6b;
  color: #f6f7f2;
  font-size: 0.85rem;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.coming-soon__title {
  margin: 0;
  color: #3d4430;
  font-size: 1.35rem;
  line-height: 1.7;
  font-weight: 700;
}

.coming-soon__subtitle {
  margin: 0;
  color: #929c6b;
  font-size: 0.95rem;
  line-height: 1.85;
}

.coming-soon__link {
  margin-top: 0.35rem;
  width: 100%;
}

.coming-soon__link :deep(.p-button) {
  width: 100%;
  color: #3d4430;
  font-weight: 700;
}

@keyframes coming-soon-fade-in {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes coming-soon-float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-6px);
  }
}

@keyframes coming-soon-pulse {
  0%,
  100% {
    transform: scale(0.96);
    opacity: 0.75;
  }
  50% {
    transform: scale(1.04);
    opacity: 1;
  }
}
</style>
