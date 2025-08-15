<script>
export default {
  name: 'Auth',
  data(){
    return {
      phone : null
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
      برای ورود، شماره تلفن خود را وارد کنید.
    </div>
    <div class="text-center mt-5">
      <InputText dir="ltr" type="text" size="large" v-model="phone" class="p-3"  />
    </div>
    <div class="text-center mt-5">
      <Button size="large" label="ورود به حساب" class="ayt-bg-light ayt-border-light" rounded />

    </div>
  </div>
</div>

</template>

<style scoped>
.login-bg {
  height: 92vh;
  background: url("src/assets/images/template/login-bg.svg");
  background-size: cover;
}
.logo-box{
  padding-top: 80px;
  padding-bottom: 35px;
}

</style>