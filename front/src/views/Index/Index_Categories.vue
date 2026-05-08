<script>
import Category_Item from "@/components/Categories/Category_Item.vue";

export default {
  name : "Index_Categories",
  components : {
    'category_item' : Category_Item
  },
  data(){
    return{
      categories : [
        {
          name : 'همه‌'
        },
        {
          name : 'قهوه‌ی گرم'
        },
        {
          name : 'قهوه‌ی سرد'
        },
        {
          name : 'قهوه‌ی دمی'
        },
        {
          name : 'چای و دمنوش'
        },
        {
          name : 'ماکتل'
        },
        {
          name : 'شیک'
        }

      ]
    }
  }


}
</script>

<template>
<div>
  <div>
    <strong class="text-black-alpha-90 font-26">دسته بندی ها</strong>
  </div>
  <div class="mt-2">
    <template v-for="item in categories">
      <category_item class="mt-3 mr-2 ml-2" :item="item.name"/>
    </template>
  </div>

</div>

</template>
