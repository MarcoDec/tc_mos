<script lang="ts" setup>
    import {computed, defineProps, reactive} from 'vue'
    import type {Items} from '../../../store/supplierItems/supplierItem/getters'

    const props = defineProps<{item: Items}>()
    const items = reactive<Items[]>([props.item])
    const total = computed(() =>
        items.reduce(
            (previousValue, currentValue) => previousValue + currentValue.quantite!,
            0
        ))
    const rest = computed(() => {
        const result = (props.item.quantite ?? 0) - total.value
        return result > 0 ? result : 0
    })

    function plus(item: Items): void {
        items.push(item)
    }
    function update(payload: {item: Items, index: number}): void {
        items[payload.index] = payload.item
    }
</script>

<template>
    <tbody>
        <AppTableItem
            v-for="(current, i) in items"
            :key="i"
            :index="i"
            :item="current"
            @update="update"/>
        <AppTableAddItem :value="rest" @plus="plus"/>
    </tbody>
    <AppTableFooter :item="item" :value="rest" @update="update"/>
</template>
