<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {computed, defineEmits, defineProps, inject, ref} from 'vue'
    import { useNamespacedGetters , useNamespacedState} from 'vuex-composition-helpers'

    const ONE = 1

    const props = defineProps<{index: number,fields:TableField[], item: string[]}>()
    const stateFields: TableField[][] = []
     const states = []
    for (const part of props.item){
        console.log('part',part);
        const tabFields: TableField[] = []
        for (const field of props.fields){
            if (part.startsWith(field.prefix) && !(Array.isArray(field.children) && field.children.length > 0)){
                tabFields.push(field)
            }
        }
        stateFields.push(tabFields)
        states.push({ 
            ...useNamespacedState(part, [...tabFields.map(({name}) => name), 'delete', 'update', 'id']),
            rowspan: useNamespacedGetters(part, ['rowspan']).rowspan.value
        })
    }
    console.log('states', states);
    console.log('stateFields', stateFields);
    
    const formattedIndex = computed(() => props.index + ONE)
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

    <tr >
        <template v-for="(state,i) in states">
            <!-- <td  :rowspan="state.rowspan" >{{ formattedIndex }}</td> -->
            <td  :rowspan="state.rowspan" >{{ state.indice }}</td>
            <td :rowspan="state.rowspan" v-if="show"  class="text-center">
                <AppBtn v-if="state.update" icon="pencil-alt" variant="primary" @click="toggle"/>
                <AppBtn v-if="state.update2" icon="eye" variant="secondary" @click="update"/>
                <AppBtn v-if="state['delete']" icon="trash" variant="danger"/>
            </td>
            <td :rowspan="state.rowspan" v-else class="text-center">
                <AppBtn icon="check" variant="success"/>
                <AppBtn icon="times" variant="danger" @click="toggle"/>
            </td>
            <component :rowspan="state.rowspan"  :is="td" v-for="field in stateFields[i]" :key="field.name" :field="field" :item="state"/>
        </template>
    </tr>
</template>
