<script setup lang="ts">
defineProps<{ status: string }>()

const statusConfig: Record<string, { label: string; color: string }> = {
  pending:    { label: 'Pending',    color: '#ff9500' },
  processing: { label: 'Processing', color: '#0071e3' },
  shipped:    { label: 'Shipped',    color: '#5e5ce6' },
  delivered:  { label: 'Delivered',  color: '#28cd41' },
  cancelled:  { label: 'Cancelled',  color: '#aeaeb2' },
}
</script>

<template>
  <span class="status" :style="{ '--dot': statusConfig[status]?.color ?? '#aeaeb2' }">
    <span class="dot" />
    {{ statusConfig[status]?.label ?? status }}
  </span>
</template>

<style scoped>
.status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.875rem;
  color: #1d1d1f;
  white-space: nowrap;
}

.dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--dot);
  flex-shrink: 0;
}
</style>
