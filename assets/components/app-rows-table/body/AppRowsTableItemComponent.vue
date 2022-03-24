<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {computed, defineEmits, defineProps, inject, ref} from 'vue'
    import { useNamespacedGetters , useNamespacedState} from 'vuex-composition-helpers'

    const props = defineProps<{ state:{}, i: number, stateFields:TableField[][]}>()
    
    const show = ref(true)
    const td = computed(() => (show.value ? 'AppRowsTableItemField' : 'AppRowsTableItemInput'))

    const emit = defineEmits<(e: 'update', item: TableItem) => void>()
    function toggle(): void {
        show.value = !show.value
    }
    function update(): void {
        emit('update', props.item)
    }
  
    
</script>

<template>
    <td  :rowspan="props.state.rowspan" >{{ props.state.index }}</td>
    <td :rowspan="props.state.rowspan" v-if="show"  class="text-center">
        <AppBtn v-if="props.state.update" icon="pencil-alt" variant="primary" @click="toggle"/>
        <AppBtn v-if="props.state.update2" icon="eye" variant="secondary" @click="update"/>
        <AppBtn v-if="props.state['delete']" icon="trash" variant="danger"/>
    </td>
    <td :rowspan="props.state.rowspan" v-else class="text-center">
        <AppBtn icon="check" variant="success"/>
        <AppBtn icon="times" variant="danger" @click="toggle"/>
    </td>
    <component :rowspan="props.state.rowspan"  :is="td" v-for="field in props.stateFields[props.i]" :key="field.name" :field="field" :item="props.state"/>
    
</template>
