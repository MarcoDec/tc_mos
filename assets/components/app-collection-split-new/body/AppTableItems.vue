<script setup>
    import {computed, defineProps, reactive} from 'vue'
    import AppTableAddItem from './AppTableAddItem.vue'
    import AppTableFooter from '../footer/AppTableFooter.vue'
    import AppTableItem from './AppTableItem.vue'

    const props = defineProps({item: {required: true, type: Object}})
    const items = reactive([props.item])
    const total = computed(() =>
        items.reduce(
            (previousValue, currentValue) => previousValue + currentValue.quantite,
            0
        ))
    const rest = computed(() => {
        const result = (props.item.quantite ?? 0) - total.value
        return result > 0 ? result : 0
    })

    function plus(item) {
        items.push(item)
    }
    function update(payload) {
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
