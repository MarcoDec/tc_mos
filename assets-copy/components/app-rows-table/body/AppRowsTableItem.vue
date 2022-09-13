<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-rows-table'
    import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import {defineProps} from 'vue'

    const props = defineProps<{item: string[], alignFields: TableField[]}>()
        console.log('item hellooo ***',props.item );
        console.log('alignFields ====',props.alignFields );

    const stateFields: TableField[][] = []
    const states: TableItem[] = []
    for (const part of props.item){
                console.log('part ====',part );

        const tabFields: TableField[] = []
        for (const field of props.alignFields){
            if (part.startsWith(field.prefix) && !(Array.isArray(field.children) && field.children.length > 0)){
                tabFields.push(field)
            }
        }
        stateFields.push(tabFields)
        states.push({
            ...useNamespacedState(part, [...tabFields.map(({name}) => name), 'delete', 'update', 'index']),
            rowspan: useNamespacedGetters(part, ['rowspan']).rowspan.value as number
        })
    }
</script>

<template>
    <tr>
        <AppRowsTableItemComponent v-for="(state, i) in states" :key="i" :i="i" :state="state" :state-fields="stateFields"/>
    </tr>
</template>
