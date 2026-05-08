<template>
  <Card>
    <template #title>نمونه فرم مدیریتی</template>
    <template #content>
      <div class="grid">
        <div class="col-12 md:col-6">
          <label class="block mb-2 font-semibold">نام</label>
          <InputText class="w-full" placeholder="مثال: نوشیدنی گرم" />
        </div>
        <div class="col-12 md:col-6">
          <label class="block mb-2 font-semibold">اسلاگ</label>
          <InputText class="w-full" placeholder="hot-drinks" />
        </div>
        <div class="col-12">
          <label class="block mb-2 font-semibold">توضیحات</label>
          <Textarea rows="3" class="w-full" />
        </div>
        <div class="col-12 flex justify-content-end gap-2">
          <Button label="انصراف" severity="secondary" outlined />
          <Button label="ذخیره" icon="pi pi-check" />
        </div>
      </div>
    </template>
  </Card>
</template>
