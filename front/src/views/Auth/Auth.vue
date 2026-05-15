<script>
import { useUserStore } from '@/stores/user'

export default {
  name: 'Auth',
  data(){
    return {
      phone: '',
      otpCode: '',
      otpSessionId: null,
      step: 'mobile',
      isLoading: false,
      errorMessage: ''
    }
  },
  methods: {
    async submit() {
      this.errorMessage = ''
      const userStore = useUserStore()
      this.isLoading = true

      try {
        if (this.step === 'mobile') {
          const response = await userStore.requestOtp({
            mobile: this.phone
          })
          this.otpSessionId = response?.otpSessionId || null
          this.step = 'otp'
          return
        }

        await userStore.verifyOtp({
          mobile: this.phone,
          otp_code: this.otpCode,
          otp_session_id: this.otpSessionId,
          device_name: 'mobile-web'
        })

        const redirect = this.$route.query?.redirect
        if (typeof redirect === 'string' && redirect.startsWith('/')) {
          await this.$router.push(redirect)
        } else {
          await this.$router.push({ name: 'profile' })
        }
      } catch (error) {
        this.errorMessage = error?.message || 'خطایی رخ داد. دوباره تلاش کنید.'
      } finally {
        this.isLoading = false
      }
    }
  },



}
</script>

<template>
<div>
  <div class="login-bg">
    <div class="text-center logo-box">
      <img src="@/assets/images/template/auth-logo.svg" alt="">
    </div>
    <div class="text-center text-black-alpha-90 mt-5 font-20">
      <template v-if="step === 'mobile'">برای ورود، شماره تلفن خود را وارد کنید.</template>
      <template v-else>کد تایید ارسال‌شده را وارد کنید.</template>
    </div>
    <div class="text-center mt-5">
      <InputText
        v-if="step === 'mobile'"
        dir="ltr"
        type="text"
        size="large"
        v-model="phone"
        class="p-3"
      />
      <InputText
        v-else
        dir="ltr"
        type="text"
        size="large"
        v-model="otpCode"
        class="p-3"
      />
    </div>
    <div v-if="errorMessage" class="text-center mt-3">
      <small class="text-red-500">{{ errorMessage }}</small>
    </div>
    <div class="text-center mt-5">
      <Button
        :loading="isLoading"
        :disabled="isLoading"
        size="large"
        :label="step === 'mobile' ? 'دریافت کد ورود' : 'ورود به حساب'"
        class="ayt-bg-light ayt-border-light"
        rounded
        @click="submit"
      />

    </div>
  </div>
</div>

</template>

<style scoped>
.login-bg {
  height: 92vh;
  background: url("@/assets/images/template/login-bg.svg");
  background-size: cover;
}
.logo-box{
  padding-top: 80px;
  padding-bottom: 35px;
}

</style>