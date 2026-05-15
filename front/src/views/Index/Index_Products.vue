<script>
import Product_Item from "@/components/Products/Product_Item.vue";
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useUserMenuStore } from '@/stores/userMenu'

export default {
  name: "Index_Products",
  components:{
    'product_item' : Product_Item,
  },
  setup() {
    const menuStore = useUserMenuStore()
    const { items } = storeToRefs(menuStore)

    const activeTitle = computed(() => menuStore.activeCategoryName || 'منو')

    return {
      items,
      activeTitle
    }
  }
}
</script>

<template>
<div>
  <div class="pl-3 pr-3 pt-2 pb-3 ayt-spacer">
    <strong class="text-black-alpha-90 font-25">{{ activeTitle }}</strong>
  </div>
  <div class="mt-4">
    <div class="grid">
      <template v-for="item in items" :key="item.id">
        <div class="md:col-6 sm:col-6 col-6">
          <product_item :product="item"></product_item>
        </div>
      </template>

    </div>
  </div>
</div>



</template>


<style scoped>

</style>