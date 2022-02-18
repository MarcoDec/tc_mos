<script lang="ts" setup>
    import {TableField} from "../../../types/app-collection-table";
    import type {TableItem} from '../../../types/app-collection-table'
    import {defineEmits, defineProps, inject, ref} from 'vue'


    const emit = defineEmits<(e: 'plus') => void>()

    const props = defineProps<{items: TableItem[]}>()
    const fields = inject<TableField[]>('fields', [])
    const add = ref(false)
    const it = ref([...props.items])

    function toggle(): void {
      add.value = !add.value
      console.log('hhhhhh',add.value)
    }
    function plus(item: TableItem): void {
      it.value.push(item)
      console.log('items--->',props.items)

    }
</script>

<template>
    <tbody>
    <AppTableItem v-for="(item, index) in items" :key="item.id" :index="index" :item="item">
      <template v-for="field in fields" v-slot:[field.name]="params">
        <slot :name="field.name" v-bind="params"/>
      </template>
    </AppTableItem>
    <AppTableAddItem v-for="(item, index) in items" :key="item.id" :index="index" :item="item" @plus="plus"/>
    </tbody>
</template>
