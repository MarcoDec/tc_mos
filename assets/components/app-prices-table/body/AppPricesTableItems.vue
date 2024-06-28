<script setup>
    import {defineProps} from 'vue'
    import AppPricesTableItem from './AppPricesTableItem.vue'

    const props = defineProps({
        items: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String}
    })
    console.log('AppPricesTableItems.vue', props)
    const emit = defineEmits(['addItemPrice', 'annuleUpdate', 'deleted', 'deletedPrices', 'updateItems', 'updateItemsPrices'])
    async function updateItems(item) {
        emit('updateItems', item)
        emit('annuleUpdate')
    }
    function annuleUpdated() {
        emit('annuleUpdate')
    }
    function addItemPrice(formData) {
        emit('addItemPrice', formData)
    }
    async function updateItemsPrices(item) {
        emit('updateItemsPrices', item)
    }
    function deleted(id){
        emit('deleted', id)
    }
    function deletedPrices(id){
        emit('deletedPrices', id)
    }
</script>

<template>
    <AppPricesTableItem
        v-for="item in items" :key="item"
        :item="item"
        :items="items"
        :form="form"
        :main-fields="mainFields"
        :price-fields="priceFields"
        @add-item-price="addItemPrice"
        @deleted="deleted"
        @deleted-prices="deletedPrices"
        @annule--update="annuleUpdated"
        @update-items="updateItems"
        @update-items-prices="updateItemsPrices"/>
</template>
