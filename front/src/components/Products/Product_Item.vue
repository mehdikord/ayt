<script >
import fallbackProductImage from '@/assets/images/template/espresso.svg'

export default {
  name: "Product_Item",
  props: {
    product: {
      type: Object,
      default: () => ({})
    }
  },
  data(){
    return{
      prices_dialog : false,
    }
  },
  computed: {
    title() {
      return this.product?.name || 'محصول'
    },
    description() {
      const text = this.product?.description?.trim()
      return text || '---'
    },
    imageUrl() {
      return this.product?.imageUrl || fallbackProductImage
    },
    firstPrice() {
      const firstVariant = this.product?.variants?.[0]
      return firstVariant?.finalPrice ?? firstVariant?.price ?? 0
    },
    variants() {
      return Array.isArray(this.product?.variants) ? this.product.variants : []
    }
  }
}
</script>

<template>
  <div>
    <div class="text-black-alpha-90 pt-3 pb-3">
      <div @click="prices_dialog = true" class="product-box">
        <div class="pr-3">
          <div class="ayt-bg-light ayt-text-dark text-center pt-1" style="width: 40px;height: 40px;border-radius: 50%;">
            <strong class="font-20">4.8</strong>
          </div>
        </div>
        <div class="text-center pt-1">
          <img :src="imageUrl" alt="product" width="130">
        </div>
        <div class="text-center mt-1">
          <strong class="font-22">{{ title }}</strong>
        </div>
        <div class="text-center ayt-text-dark font-13 mt-1 pl-3 pr-3">
          {{ description }}
        </div>
        <div class="mt-3">
          <div class="text-center">
            <span>از</span><strong class="font-17 mr-1">{{ firstPrice.toLocaleString('fa-IR') }}</strong>
            <img class="mr-2" src="@/assets/images/template/currency.svg" width="16" />
          </div>
        </div>
      </div>
    </div>
    <Dialog :closable="false" class="bg-white" v-model:visible="prices_dialog" modal style="width: 90% ;max-width: 450px;" :draggable="false" position="center">
      <div class="text-center">
        <img :src="imageUrl" width="170" alt="product" />
      </div>
      <div class="mt-2">
        <strong class="text-black-alpha-90 font-24">{{ title }}</strong>
        <strong  class="ayt-text-dark  font-18 ayt-bg-light p-2" style="float: left ;border-radius: 50%">4.8</strong>
      </div>
      <div class="mt-2 ayt-text-dark">
        {{ description }}
      </div>
      <div class="mt-5">
        <template v-for="(variant, index) in variants" :key="variant.id || index">
          <div class="">
            <div class="grid">
              <div class="col">
                <strong class="text-black-alpha-90">{{ variant.name }}</strong>
              </div>
              <div class="col text-left ">
                <div class="mt-1">
                  <strong class="text-black-alpha-90 font-16">{{ variant.finalPrice.toLocaleString('fa-IR') }}</strong><span class="text-black-alpha-70 mr-2 font-12">تومان</span>
                </div>
              </div>
            </div>
          </div>
          <Divider v-if="index < variants.length - 1" type="dashed" />
        </template>
        <div v-if="!variants.length" class="text-center text-black-alpha-60">
          قیمتی برای این محصول ثبت نشده است.
        </div>


      </div>
      <div class="mt-3 text-center">
        <Button @click="prices_dialog=false" icon="pi pi-times" severity="danger" rounded variant="outlined" aria-label="Cancel" />
      </div>

    </Dialog>
  </div>
</template>

<style scoped>
.product-box{
  height: 280px;background-image:url('@/assets/images/template/product-shape.svg');background-size: 95%;background-position: center;background-repeat: no-repeat
}
</style>