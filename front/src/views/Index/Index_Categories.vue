<script>
import Category_Item from "@/components/Categories/Category_Item.vue";
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useUserMenuStore } from '@/stores/userMenu'

const ALL_CATEGORIES_LABEL = 'همه دسته بندی ها'

export default {
  name : "Index_Categories",
  components : {
    'category_item' : Category_Item
  },
  setup() {
    const menuStore = useUserMenuStore()
    const { categories, selectedCategoryId, isAllCategoriesMode } = storeToRefs(menuStore)

    onMounted(() => {
      if (!categories.value.length) {
        menuStore.loadCategories()
      }
    })

    const onSelectAllCategories = () => {
      menuStore.selectAllCategories()
    }

    const onSelectCategory = (categoryId) => {
      menuStore.selectCategory(categoryId)
    }

    return {
      categories,
      selectedCategoryId,
      isAllCategoriesMode,
      allCategoriesLabel: ALL_CATEGORIES_LABEL,
      onSelectAllCategories,
      onSelectCategory
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
    <category_item
      class="mt-3 mr-2 ml-2"
      :item="allCategoriesLabel"
      :active="isAllCategoriesMode"
      @select="onSelectAllCategories"
    />
    <template v-for="item in categories" :key="item.id">
      <category_item
        class="mt-3 mr-2 ml-2"
        :item="item.name"
        :active="selectedCategoryId === item.id"
        @select="onSelectCategory(item.id)"
      />
    </template>
  </div>
</div>

</template>
