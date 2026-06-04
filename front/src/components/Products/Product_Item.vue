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
        <div class="product-box__rating pr-3">
          <div class="ayt-bg-light ayt-text-dark text-center pt-1 product-box__rating-badge">
            <strong class="font-20">4.8</strong>
          </div>
        </div>
        <div class="product-box__image-wrap">
          <img :src="imageUrl" alt="product" class="product-box__image" />
        </div>
        <div class="product-box__body">
          <div class="text-center">
            <strong class="font-18">{{ title }}</strong>
          </div>
          <!-- <div class="text-center ayt-text-dark font-13 mt-1 pl-2 pr-2 product-box__description">
            {{ description }}
          </div> -->
          <div class="mt-2 product-box__price">
            <div class="text-center">
              <span>از</span><strong class="font-17 mr-1">{{ firstPrice.toLocaleString('fa-IR') }}</strong>
              <img class="mr-2" src="@/assets/images/template/currency.svg" width="16" alt="" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <Dialog :closable="false" class="bg-white" v-model:visible="prices_dialog" modal style="width: 90% ;max-width: 450px;" :draggable="false" position="center">
      <div class="product-dialog__image-wrap text-center">
        <img :src="imageUrl" alt="product" class="product-dialog__image" />
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
.product-box {
  height: 280px;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  box-sizing: border-box;
  padding: 0.35rem 0.5rem 0.5rem;
  background-image: url('@/assets/images/template/product-shape.svg');
  background-size: 95%;
  background-position: center;
  background-repeat: no-repeat;
  cursor: pointer;
}

.product-box__rating {
  flex-shrink: 0;
  z-index: 1;
}

.product-box__rating-badge {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.product-box__image-wrap {
  flex: 1 1 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 88px;
  padding: 0 0.5rem;
  margin-top: -0.15rem;
}

.product-box__image {
  display: block;
  width: 90%;
  height: 100%;
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  object-position: center;
}

.product-box__body {
  flex-shrink: 0;
  width: 100%;
}

.product-box__description {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.35;
}

.product-dialog__image-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 160px;
  padding: 0.5rem;
}

.product-dialog__image {
  width: 100%;
  /* max-width: 280px; */
  /* max-height: 220px; */
  height: auto;
  object-fit: contain;
  object-position: center;
}
</style>