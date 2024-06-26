<script setup>
    import {defineProps} from 'vue'
    import AppPricesTableAddItems from './AppPricesTableAddItems.vue'
    import AppPricesTableItems from './AppPricesTableItems.vue'

    defineProps({
        fieldsComponentSuppliers: {required: true, type: Array},
        fieldsComponentSuppliersPrices: {required: true, type: Array},
        form: {required: true, type: String},
        items: {required: true, type: Object}
    })
    const emit = defineEmits(['addItem', 'addItemPrice', 'annuleUpdate', 'deleted', 'deletedPrices', 'updateItems', 'updateItemsPrices'])
    async function updateItems(item) {
        emit('updateItems', item)
        emit('annuleUpdate')
    }
    function annuleUpdated() {
        emit('annuleUpdate')
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
    function addItem(formData) {
        emit('addItem', formData)
    }
    function addItemPrice(formData) {
        emit('addItemPrice', formData)
    }
</script>

<template>
    <tbody>
        <AppPricesTableItems :fields-component-suppliers="fieldsComponentSuppliers" :fields-component-suppliers-prices="fieldsComponentSuppliersPrices" :form="form" :items="items" @deleted="deleted" @deleted-prices="deletedPrices" @add-item-price="addItemPrice" @annule-update="annuleUpdated" @update-items="updateItems" @update-items-prices="updateItemsPrices"/>
        <AppPricesTableAddItems :fields="fieldsComponentSuppliers" :form="form" @add-item="addItem"/>
    </tbody>
</template>
