<script lang="ts" setup>
  import type {TableField, TableItem} from '../../../types/app-collection-table'
  import {computed, defineEmits, defineProps, inject, ref} from 'vue'


  const emit = defineEmits<(e: 'plus') => void>()
  const props = defineProps<{ index: number, item: TableItem }>()
  const fields = inject<TableField[]>('fields', [])
  const show = ref(false)
  const td = computed(() => (show.value ? 'AppCollectionTableItemField' : 'AppCollectionTableItemInput'))
  const value = ref()

  function plus(): void {
    emit('plus',)
  }
</script>

<template>
  <tr>
    <td class="text-center">
    </td>
    <td class="text-center">
      <AppBtn v-if="item.update" icon="plus" variant="primary" @click="plus"/>
    </td>
    <template v-for="field in fields">
      <slot :name="field.name" :field="field" :item="item">
        <component :is="td" :field="field" :item="item"/>
      </slot>
    </template>
  </tr>

  <tr class="text-center">
    <td>
    </td>
    <td>
      <span>Total confirmé:</span>
    </td>
    <td>
      <span>Reste:</span>
    </td>
  </tr>

</template>
