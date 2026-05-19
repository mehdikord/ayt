<script>

export default {

  name: "Category_Item",

  props: {

    item: {

      type: String,

      default: ''

    },

    active: {

      type: Boolean,

      default: false

    }

  },

  emits: ['select']

}

</script>



<template>

  <Chip

    :style="active

      ? { backgroundColor: 'black', border: '1px solid black', cursor: 'pointer', color: 'white' }

      : { backgroundColor: 'transparent', border: '1px solid black', cursor: 'pointer' }"

    :class="active ? 'text-white' : 'text-black-alpha-90'"

    @click="$emit('select')"

  >

    {{ item }}

  </Chip>



</template>



<style scoped>



</style>
