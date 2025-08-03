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
<!-- کانتینر مرکزی -->
<div class="w-full shadow-2 flex flex-column" style="max-width: 480px; min-height: 100vh;">

  <!-- هدر -->
  <header class="header-color pt-3 pb-2 pl-4 pr-4 border-bottom-1 text-center border-round-bottom-3xl">
    <template_header/>
  </header>
  <div class="text-center">
    <img src="@/assets/images/template/header-logo.svg" alt="Image"  style="margin-top: -80px;" width="80" />
  </div>
  <!-- محتوای اصلی -->
  <main class="flex-1 p-3">
    <router-view />
  </main>


  <!-- فوتر -->
  <footer class="p-2 border-top-1 surface-border text-center text-sm">
    © 2025 اپ من
  </footer>

</div>
</div>
</template>
<style scoped>

</style>
