<template>
  <Card>
    <template #title>وضعیت ها</template>
    <template #content>
      <div class="flex flex-wrap gap-2">
        <Tag value="فعال" severity="success" />
        <Tag value="غیرفعال" severity="danger" />
        <Tag value="منقضی" severity="warn" />
        <Tag value="پیش نویس" severity="secondary" />
      </div>
    </template>
  </Card>
</template>
