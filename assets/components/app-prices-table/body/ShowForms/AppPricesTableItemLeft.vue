<script setup>
    import {computed, defineProps, ref} from 'vue'
    import AppPricesTableUpdateItem from '../UpdateForms/AppPricesTableUpdateItem.vue'
    import AppMainItemShow from './MainItemForms/AppMainItemShow.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String},
        index: {required: true, type: Number}
    })
    const emit = defineEmits(['deleted', 'update', 'annuleUpdate', 'updateItems'])
    console.log('props.item', props.item)
    const updated = ref(false)

    function update(item) {
        console.log('update', item)
        //emit('update', item)
        updated.value = true
    }

    function annuleUpdated() {
        emit('annuleUpdate')
        updated.value = false
    }

    function deleted(item) {
        console.log('deleted', item)
        emit('deleted', item)
    }

    async function updateItems(item) {
        console.log('item', item)
        emit('updateItems', item)
        emit('annuleUpdate')
    }

    const nbTr = computed(() => {
        const nbItems = props.item.prices.length
        if (nbItems > 0) return nbItems
        return 1
    })

    function range(n) {
        return Array.from({length: n}, (value, key) => key + 1)
    }
</script>

<template>
    <AppMainItemShow
        v-if="updated === false"
        :index="index"
        :form="`main_${props.item.id}`"
        :main-fields="mainFields"
        :item="item"
        @deleted="deleted"
        @update="update"/>
    <AppPricesTableUpdateItem
        v-else
        :fields="mainFields"
        :form="form"
        :item="item"
        :index="index"
        @annule-update="annuleUpdated"
        @update-items="updateItems"/>
</template>
