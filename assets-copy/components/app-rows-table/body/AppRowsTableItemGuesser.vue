<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {TableField} from '../../../types/app-rows-table'

    const props = defineProps<{last: {required: true, type: boolean}, item: number, fields: TableField[], fieldsByLevel: TableField[][], alignFields: TableField[], index: number}>()
    const add = computed(() => typeof props.item === 'number')
</script>

<template>
    <AppRowsTableAddItems v-if="add" :fields="fieldsByLevel[item]"/>
    <AppRowsTableItem v-else :last="last" :fields="fields" :align-fields="alignFields" :item="item" :index="index"/>
</template>
