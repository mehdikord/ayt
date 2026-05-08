<script>
import Index_Categories from "@/views/Index/Index_Categories.vue";
import Index_Products from "@/views/Index/Index_Products.vue";

export default {
  name : "Index",
  components:{
    'categories' : Index_Categories,
    'products' : Index_Products,
  }


}
</script>

<template>
  <categories/>
  <div class="mt-5"></div>
  <products></products>


</template>
