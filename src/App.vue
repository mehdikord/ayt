<script>
import Template_Header from "@/components/Template/Template_Header.vue";

export default {
  name : "App",
  components : {
    'template_header' : Template_Header,
  }
}
</script>
<template>
<div class="flex justify-content-center bg-gray-100 min-h-screen">
<div class="w-full shadow-2 flex flex-column" style="max-width: 480px; min-height: 100vh;">

  <header class="header-color pt-3 pb-2 pl-4 pr-4 border-bottom-1 text-center border-round-bottom-3xl">
    <template_header/>
  </header>
  <div class="text-center">
    <img src="@/assets/images/template/header-logo.svg" alt="Image"  style="margin-top: -80px;" width="80" />
  </div>
  <main class="flex-1 p-3">
    <router-view />
  </main>


  <footer class="pt-3 pb-1 pl-3 pr-3 bg-black-alpha-90 border-round-top-3xl">
    <div class="grid">
      <div class="col-3 text-center">
        <Image src="src/assets/images/template/menu/menu-menu.svg" width="32" />
        <div>
          <strong class="text-color-secondary">منو</strong>
        </div>
      </div>
      <div class="col-3 text-center">
        <Image src="src/assets/images/template/menu/menu-profile.svg" width="30" />
        <div>
          <strong class="text-color-secondary">پروفایل</strong>
        </div>
      </div>
      <div class="col-3 text-center">ad</div>
      <div class="col-3 text-center">ad</div>
    </div>
  </footer>

</div>
</div>
</template>
<style scoped>

</style>
