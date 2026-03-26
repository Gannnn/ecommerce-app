<script setup lang="ts">
defineProps<{ status: string }>()

const statusConfig: Record<string, { label: string; color: string }> = {
  pending:    { label: 'Pending',    color: '#ff9500' },
  processing: { label: 'Processing', color: '#0071e3' },
  shipped:    { label: 'Shipped',    color: '#5e5ce6' },
  delivered:  { label: 'Delivered',  color: '#28cd41' },
  cancelled:  { label: 'Cancelled',  color: '#ff3b30' },
}
</script>

<template>
  <span
    class="badge"
    :style="{
      backgroundColor: (statusConfig[status]?.color ?? '#999') + '20',
      color: statusConfig[status]?.color ?? '#999',
    }"
  >
    {{ statusConfig[status]?.label ?? status }}
  </span>
</template>

<style scoped>
.badge {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 980px;
  text-transform: capitalize;
}
</style>
