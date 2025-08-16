<script>

export default {
  name: "Profile"
}
</script>

<template>
  <div class="pt-5">
    <div class="grid p-3">
      <div class="col-4">
        <div class="profile text-center">
          <img src="@/assets/images/template/profile-sample.png">
        </div>
      </div>
      <div class="col-8">
        <div>
          <strong class="text-black-alpha-90 font-26">سلام مهدی عزیز !</strong>
        </div>
        <div class="mt-3">
          <span class="ayt-text-dark font-18">کد اشتراک : </span><strong class="font-20 mr-2 text-black-alpha-90">254</strong>
        </div>
      </div>
    </div>
    <Card class="mt-5 mb-0 ayt-bg-dark ayt-border-dark">
      <template #content>
        <div class="grid">
          <div class="col-7">
            <div>
              <strong class="text-black-alpha-90 font-22">محبوب ترینِ شما</strong>
            </div>
            <div class="mt-3 ayt-text-light font-16">
              اسپرسو ۱۰۰٪ ‌عربیکا
            </div>
          </div>
          <div class="col-5">
            <div class="text-center">
              <img src="@/assets/images/template/espresso.svg" alt="">
            </div>
          </div>
        </div>
      </template>

    </Card>
    <div class="grid mt-4">
      <div class="col-6">
        <div class="p-3 bg-white border-round-2xl">
          <div class="text-left">
            <img src="@/assets/images/template/profile-user-icon.svg" alt="">
          </div>
          <div class="mt-2 mb-2">
            <strong class="ayt-text-dark font-17">
              ویرایش اطلاعات کاربر
            </strong>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="p-3 bg-white border-round-2xl">
          <div class="text-left">
            <img src="@/assets/images/template/profile-off-icon.svg" alt="">
          </div>
          <div class="mt-2 mb-2">
            <strong class="ayt-text-dark font-17">
              تخفیف‌ها و پیشنهاد‌ ها
            </strong>
          </div>
        </div>
      </div>
    </div>
    <div class="logout-box">
      <div class="text-left">
        <Button class="text-white" severity="danger" rounded>خروج</Button>
      </div>
    </div>

  </div>
</template>

<style scoped>
.profile{
  background: url("@/assets/images/template/profile-bg.svg");
  width: 100px;
  height: 103px;
}

.logout-box{
  margin-top: 70px;
}

</style>