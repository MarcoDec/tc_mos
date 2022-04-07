<script lang="ts" setup>
    import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import type {TableField} from '../../../types/app-rows-table'
    import {defineProps} from 'vue'

    const props = defineProps<{item: string[], alignFields: TableField[]}>()
    const stateFields: TableField[][] = []
    const states: TableField[] = []
    for (const part of props.item){
        const tabFields: TableField[] = []
        for (const field of props.alignFields){
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
</script>

<template>
    <tr>
        <template v-for="(state, i) in states">
            <AppRowsTableItemComponent :i="i" :state="state" :state-fields="stateFields"/>
        </template>
    </tr>
</template>
