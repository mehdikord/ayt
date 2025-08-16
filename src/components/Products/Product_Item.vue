<script >


export default {
  name: "Product_Item",
  data(){
    return{
      prices_dialog : false,
    }
  }
}
</script>

<template>
  <div>
    <div class="text-black-alpha-90 pt-3 pb-3">
      <div @click="prices_dialog = true" style="height: 280px;background-image:url('../../../src/assets/images/template/product-shape.svg');background-size: 95%;background-position: center;background-repeat: no-repeat">
        <div class="pr-3">
          <div class="ayt-bg-light ayt-text-dark text-center pt-1" style="width: 40px;height: 40px;border-radius: 50%;">
            <strong class="font-20">4.8</strong>
          </div>
        </div>
        <div class="text-center pt-1">
          <img src="@/assets/images/template/espresso.svg" alt="product" width="130">
        </div>
        <div class="text-center mt-1">
          <strong class="font-22">کاپوچینو</strong>
        </div>
        <div class="text-center ayt-text-dark font-13 mt-1 pl-3 pr-3">
          اسپرسو / شیر با دمای ۷۰ درجه / فوم شیر
        </div>
        <div class="mt-3">
          <div class="text-center">
            <span>از</span><strong class="font-17 mr-1">85,000</strong>
            <IMG class="mr-2" src="@/assets/images/template/currency.svg" width="16" />
          </div>
        </div>
      </div>
    </div>
    <Dialog :closable="false" class="bg-white" v-model:visible="prices_dialog" modal style="width: 90% ;max-width: 450px;" :draggable="false" position="center">
      <div class="text-center">
        <img src="@/assets/images/template/espresso.svg" width="170" alt="product" />
      </div>
      <div class="mt-2">
        <strong class="text-black-alpha-90 font-24">کاپوچینو</strong>
        <strong  class="ayt-text-dark  font-18 ayt-bg-light p-2" style="float: left ;border-radius: 50%">4.8</strong>
      </div>
      <div class="mt-2 ayt-text-dark">
        قهوه‌ی عصاره‌گیری شده توسط ماشین اسپرسو
      </div>
      <div class="mt-5">
        <div class="">
          <div class="grid">
            <div class="col">
              <strong class="text-black-alpha-90">۱۰۰٪ عربیکا</strong>
            </div>
            <div class="col text-left ">
              <div class="mt-1">
                <strong class="text-black-alpha-90 font-16">90,000</strong><span class="text-black-alpha-70 mr-2 font-12">تومان</span>
              </div>
            </div>
          </div>
        </div>
        <Divider type="dashed"/>
        <div class="">
          <div class="grid">
            <div class="col">
              <strong class="text-black-alpha-90">۱۰۰٪ عربیکا</strong>
            </div>
            <div class="col text-left ">
              <div class="mt-1">
                <strong class="text-black-alpha-90 font-16">90,000</strong><span class="text-black-alpha-70 mr-2 font-12">تومان</span>
              </div>
            </div>
          </div>
        </div>
        <Divider type="dashed"/>
        <div class="">
          <div class="grid">
            <div class="col">
              <strong class="text-black-alpha-90">۱۰۰٪ عربیکا</strong>
            </div>
            <div class="col text-left ">
              <div class="mt-1">
                <strong class="text-black-alpha-90 font-16">90,000</strong><span class="text-black-alpha-70 mr-2 font-12">تومان</span>
              </div>
            </div>
          </div>
        </div>
        <Divider type="dashed"/>
        <div class="">
          <div class="grid">
            <div class="col">
              <strong class="text-black-alpha-90">۱۰۰٪ عربیکا</strong>
            </div>
            <div class="col text-left ">
              <div class="mt-1">
                <strong class="text-black-alpha-90 font-16">90,000</strong><span class="text-black-alpha-70 mr-2 font-12">تومان</span>
              </div>
            </div>
          </div>
        </div>


      </div>
      <div class="mt-3 text-center">
        <Button @click="prices_dialog=false" icon="pi pi-times" severity="danger" rounded variant="outlined" aria-label="Cancel" />
      </div>

    </Dialog>
  </div>
</template>

<style scoped>

</style>