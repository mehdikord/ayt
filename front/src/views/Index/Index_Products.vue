<script>

import Product_Item from "@/components/Products/Product_Item.vue";

import MenuContentLoader from "@/components/Menu/MenuContentLoader.vue";

import { computed } from 'vue'

import { storeToRefs } from 'pinia'

import { useUserMenuStore } from '@/stores/userMenu'



export default {

  name: "Index_Products",

  components:{

    'product_item' : Product_Item,

    MenuContentLoader,

  },

  setup() {

    const menuStore = useUserMenuStore()

    const { displaySections, isContentLoading, selectedCategoryId, activeCategoryName, error } =

      storeToRefs(menuStore)



    const loadingMessage = computed(() => {

      if (selectedCategoryId.value && menuStore.isLoadingItems) {

        const name = activeCategoryName.value

        return name ? `در حال بارگذاری ${name}...` : 'در حال بارگذاری محصولات...'

      }

      return 'در حال آماده‌سازی منو...'

    })



    return {

      displaySections,

      isContentLoading,

      loadingMessage,

      error

    }

  }

}

</script>



<template>

<div>

  <MenuContentLoader v-if="isContentLoading" :message="loadingMessage" />



  <div v-else-if="error" class="menu-products-message text-center p-4 ayt-text-dark">

    خطا در بارگذاری منو. لطفاً دوباره تلاش کنید.

  </div>



  <div v-else-if="!displaySections.length" class="menu-products-message text-center p-4 ayt-text-dark">

    محصولی برای نمایش وجود ندارد.

  </div>



  <template v-else>

    <template v-for="(section, sectionIndex) in displaySections" :key="section.category?.id">

      <div :class="{ 'mt-5': sectionIndex > 0 }">

        <div class="pl-3 pr-3 pt-2 pb-3 ayt-spacer">

          <strong class="text-black-alpha-90 font-25">{{ section.category?.name }}</strong>

        </div>

        <div class="mt-4">

          <div class="grid">

            <template v-for="item in section.items" :key="item.id">

              <div class="md:col-6 sm:col-6 col-6">

                <product_item :product="item"></product_item>

              </div>

            </template>

          </div>

          <div

            v-if="!section.items.length"

            class="text-center ayt-text-dark font-14 py-4"

          >

            محصولی در این دسته ثبت نشده است.

          </div>

        </div>

      </div>

    </template>

  </template>

</div>

</template>



<style scoped>

.menu-products-message {

  font-size: 1rem;

  line-height: 1.7;

}

</style>

