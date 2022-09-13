<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-rows-table'
    import {computed, defineProps} from 'vue'

    const props = defineProps<{items: TableItem[], fields: TableField[], alignFields: TableField[]}>()
    const lengths = computed <number[]>(() => props.items.map(item => item.length))
    const max = computed <number>(() => Math.max(...lengths.value))
    const lasts = computed <number[]>(() => {
        const lastindexes: number[] = []
        if (lengths.value.length === 0){
            return lastindexes
        }
        for (let i = 1, j = 2; j < lengths.value.length; i++, j++){
            if (lengths.value[i] !== lengths.value[j] || lengths.value[i] === lengths.value[j] && lengths.value[i] === max.value){
                lastindexes.push(i)
            }
        }
        const last = lengths.value.length - 1
        if (!lastindexes.includes(last)){
            lastindexes.push(last)
        }
        return lastindexes
    })
   console.log('lasts',lasts);

    const fieldsByLevel = computed<TableField[][]>(() => {
        const rows: TableField[][] = []
        let next: TableField[] = []
        let hasChild = false
        do {
            const current = next.length > 0 ? next : props.fields
            next = []
            hasChild = false
            const row = []
            for (const field of current) {
                row.push(field)
                if (Array.isArray(field.children) && field.children.length > 0) {
                    next.push(...field.children)
                    hasChild = true
                }
            }
            rows.push(row)
        } while (hasChild)
        return rows
    })
   console.log('fieldsByLevel',fieldsByLevel);

    const levels = computed(() => {
        const levelObj = {}
        for (let i = fieldsByLevel.value.length - 1, j = 1; i > 0; i--, j++)
            levelObj[i] = j
        return levelObj
    })
    const itemsWithGhosts = computed<(TableItem | number)[]>(() => {
        const result: (TableItem | number)[] = []
        for (let i = 0, j = 1; i < props.items.length; i++, j++) {
            console.log('iiii', props.items);
                        console.log('items[i]', props.items[i]);
                        console.log('items[j]', props.items[j]);

            result.push(props.items[i])
            if (j === props.items.length || props.items[j].length >= props.items[i].length) {
                for (const k in levels.value){
                    if (props.items[i].length >= k){
                        result.push(levels.value[k])
                    }
                }
            }
        }
        result.push(0)
        console.log('result', result);
        
        return result
    })
    console.log('itemsWithGhosts', itemsWithGhosts);
    
</script>

<template>
    <tbody>
        <AppRowsTableItemGuesser v-for="(item, index) in itemsWithGhosts" :key="item.id" :last="lasts.includes(index)" :item="item" :fields="fields" :align-fields="alignFields" :items="items" :fields-by-level="fieldsByLevel" :index="index"/>
    </tbody>
</template>
