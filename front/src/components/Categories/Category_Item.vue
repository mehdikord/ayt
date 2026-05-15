<script>
export default {
  name: "Category_Item",
  props: ['item'],
  emits: ['select']
}
</script>

<template>
  <Chip
    style="background-color: transparent; border:1px solid black; cursor: pointer"
    class="text-black-alpha-90"
    @click="$emit('select')"
  >
    {{ item }}
  </Chip>

</template>

<style scoped>

</style>