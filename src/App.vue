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
  <template v-if="this.$route.name !== 'auth'">
    <header class="header-color pt-3 pb-2 pl-4 pr-4 border-bottom-1 text-center border-round-bottom-3xl header">
      <template_header/>
    </header>
    <div class="text-center header">
      <img src="@/assets/images/template/header-logo.svg" alt="Image"  style="margin-top:-85px;" width="80" />
    </div>
  </template>
  <main class="flex-1 " :class="{'p-3' : this.$route.name !== 'auth'}">
    <router-view />
  </main>

  <footer class="pt-3 pb-0 pl-3 pr-3 bg-black-alpha-90 border-round-top-3xl footer">
    <div class="grid">
      <div class="col-3 text-center">
        <router-link :to="{name: 'index'}">
          <img v-if="this.$route.name === 'index'" src="@/assets/images/template/menu/menu-menu-active.svg" class="menu-icon" />
          <img v-else src="@/assets/images/template/menu/menu-menu.svg" class="menu-icon" />
          <div>
            <strong class="text-color-secondary font-13">منو</strong>
          </div>
        </router-link>
      </div>
      <div class="col-3 text-center">
        <router-link :to="{name: 'auth'}">
          <img v-if="this.$route.name === 'auth'" src="@/assets/images/template/menu/menu-profile-active.svg" class="menu-icon" />
          <img  v-else src="@/assets/images/template/menu/menu-profile.svg" class="menu-icon" />
          <div>
            <strong class="text-color-secondary font-13">پروفایل</strong>
          </div>
        </router-link>
      </div>
      <div class="col-3 text-center">
        <router-link :to="{name: 'discounts'}">
          <img v-if="this.$route.name === 'discounts'" src="@/assets/images/template/menu/menu-off-active.svg" class="menu-icon" />
          <img v-else src="@/assets/images/template/menu/menu-off.svg" class="menu-icon" />
          <div>
            <strong class="text-color-secondary font-13">تخفیف ها</strong>
          </div>
        </router-link>

      </div>
      <div class="col-3 text-center">
        <router-link :to="{name: 'about'}">
          <img v-if="this.$route.name === 'about'" src="@/assets/images/template/menu/menu-about-active.svg" class="menu-icon" />
          <img v-else src="@/assets/images/template/menu/menu-about.svg" class="menu-icon" />
          <div>
            <strong class="text-color-secondary font-13">درباره آ‌ی‌ت</strong>
          </div>
        </router-link>
      </div>
    </div>
  </footer>

</div>
</div>
</template>
<style scoped>

.footer {
  position: sticky;
  bottom: 0;
  left: 0;

}
.menu-icon{
  width:32px!important;
  height:32px!important;
}
</style>
