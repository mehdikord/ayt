<script>
import { mapState, mapActions } from 'pinia'
import { useUserStore } from '@/stores/user'
import { toEnglishDigits } from '@/utils/digits'

export default {
  name: 'Auth',
  data(){
    return {
      otpCode: '',
      isLoading: false,
      errorMessage: ''
    }
  },
  computed: {
    ...mapState(useUserStore, ['authFlow']),
    step() {
      return this.authFlow.step
    },
    phone: {
      get() {
        return this.authFlow.phone
      },
      set(value) {
        this.setAuthFlowPhone(value)
      }
    },
    otpSessionId() {
      return this.authFlow.otpSessionId
    }
  },
  methods: {
    ...mapActions(useUserStore, [
      'requestOtp',
      'verifyOtp',
      'setAuthFlowOtp',
      'resetAuthFlowToMobile',
      'setAuthFlowPhone'
    ]),
    editMobile() {
      this.errorMessage = ''
      this.otpCode = ''
      this.resetAuthFlowToMobile()
    },
    async submit() {
      this.errorMessage = ''
      const userStore = useUserStore()
      this.isLoading = true

      try {
        const mobile = toEnglishDigits(this.phone).trim()
        const otpCode = toEnglishDigits(this.otpCode).trim()

        if (this.step === 'mobile') {
          this.setAuthFlowPhone(mobile)
          const response = await userStore.requestOtp({
            mobile
          })
          this.setAuthFlowOtp({
            phone: mobile,
            otpSessionId: response?.otpSessionId || null
          })
          return
        }

        this.otpCode = otpCode
        await userStore.verifyOtp({
          mobile,
          otp_code: otpCode,
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
  }
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
    <div v-if="step === 'otp'" class="text-center mt-3">
      <button type="button" class="edit-mobile-link" @click="editMobile">
        ویرایش شماره موبایل
      </button>
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
.edit-mobile-link {
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
  font-family: inherit;
  font-size: 0.95rem;
  color: #929C6B;
  text-decoration: underline;
}
</style>
