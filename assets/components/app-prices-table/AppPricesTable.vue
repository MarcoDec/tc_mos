<script setup>
    import {defineProps} from 'vue'
    import AppPricesTableHeaders from './head/AppPricesTableHeaders.vue'
    import AppPricesTableBody from './body/AppPricesTableBody.vue'

    defineProps({
        fieldsComponentSuppliers: {required: true, type: Array},
        fieldsComponentSuppliersPrices: {required: true, type: Array},
        form: {required: true, type: String},
        id: {required: true, type: String},
        items: {required: true, type: Object}
    })

    const emit = defineEmits(['addItem', 'addItemPrice', 'annuleUpdate', 'deleted', 'deletedPrices', 'updateItems', 'updateItemsPrices'])

    function annuleUpdated() {
        emit('annuleUpdate')
    }
    async function updateItems(item) {
        emit('updateItems', item)
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
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppPricesTableHeaders :fields-component-suppliers="fieldsComponentSuppliers"/>
        <AppPricesTableBody :fields-component-suppliers="fieldsComponentSuppliers" :fields-component-suppliers-prices="fieldsComponentSuppliersPrices" :form="form" :items="items" @deleted="deleted" @deleted-prices="deletedPrices" @annule-update="annuleUpdated" @update-items="updateItems" @update-items-prices="updateItemsPrices" @add-item="addItem" @add-item-price="addItemPrice"/>
    </table>
    <slot name="btn"/>
</template>
