<script>
export default {
  name: "Category_Item",
  props:['item']
}
</script>

<template>
  <Chip style="background-color: transparent; border:1px solid black" class="text-black-alpha-90">{{ item}}</Chip>

</template>

<style scoped>

</style>