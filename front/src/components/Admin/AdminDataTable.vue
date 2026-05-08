<script setup>
defineProps({
  title: {
    type: String,
    default: ''
  },
  rows: {
    type: Array,
    default: () => []
  },
  columns: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <Card>
    <template #title>
      <div class="flex justify-content-between align-items-center gap-2">
        <span>{{ title }}</span>
        <Button icon="pi pi-plus" label="افزودن" severity="contrast" size="small" />
      </div>
    </template>
    <template #content>
      <DataTable :value="rows" paginator :rows="5" stripedRows responsiveLayout="scroll">
        <Column v-for="col in columns" :key="col.field" :field="col.field" :header="col.header" />
        <Column header="وضعیت">
          <template #body="{ data }">
            <Tag :value="data.statusLabel" :severity="data.statusSeverity" />
          </template>
        </Column>
      </DataTable>
    </template>
  </Card>
</template>
