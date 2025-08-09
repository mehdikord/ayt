<script>


import Product_Item from "@/components/Products/Product_Item.vue";

export default {
  name: "Index_Products",
  components:{
    'product_item' : Product_Item,
  }
}
</script>

<template>
<div>
  <div class="pl-3 pr-3 pt-2 pb-3 ayt-spacer">
    <strong class="text-black-alpha-90 font-25">قهوه‌ی گرم</strong>
  </div>
  <div class="mt-4">
    <div class="grid">
      <template v-for="i in 4">
        <div class="md:col-6 sm:col-6 col-6">
          <product_item></product_item>
        </div>
      </template>

    </div>
  </div>
</div>



</template>


<style scoped>

</style>