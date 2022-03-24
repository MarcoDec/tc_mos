<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-rows-table'
    import {computed, defineEmits, defineProps, inject, ref} from 'vue'
    import { useNamespacedGetters , useNamespacedState} from 'vuex-composition-helpers'

    const props = defineProps<{index: number,fields:TableField[], item: string[]}>()
    const stateFields: TableField[][] = []
     const states = []
    for (const part of props.item){
        const tabFields: TableField[] = []
        for (const field of props.fields){
            if (part.startsWith(field.prefix) && !(Array.isArray(field.children) && field.children.length > 0)){
                tabFields.push(field)
            }
        }
        stateFields.push(tabFields)
        states.push({ 
            ...useNamespacedState(part, [...tabFields.map(({name}) => name), 'delete', 'update', 'index']),
            rowspan: useNamespacedGetters(part, ['rowspan']).rowspan.value
        })
    }
    console.log('states', states);
    console.log('stateFields', stateFields);
</script>

<template>
    <tr >
        <template v-for="(state,i) in states">
            <AppRowsTableItemComponent :i="i" :state="state"  :stateFields="stateFields" />
        </template>
    </tr>
    
</template>
