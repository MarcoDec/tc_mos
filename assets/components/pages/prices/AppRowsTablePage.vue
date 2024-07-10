<script setup>
    import AppPricesTable from '../../app-prices-table/AppPricesTable.vue'
    import AppSuspense from '../../AppSuspense.vue'

    /*const props = */defineProps({
        defaultAddFormValues: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        items: {required: true, type: Object},
        title: {required: true, type: String}
    })
    const emit = defineEmits(['addItem', 'addItemPrice', 'annuleUpdate', 'deleted', 'deletedPrices', 'updateItems', 'updateItemsPrices'])

    async function annuleUpdated() {
        emit('annuleUpdate')
    }
    async function updateItems(item) {
        emit('updateItems', item)
        emit('annuleUpdate')
    }
    async function updateItemsPrices(item) {
        emit('updateItemsPrices', item)
    }
    async function deleted(id){
        emit('deleted', id)
    }
    async function deletedPrices(id){
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
    <AppSuspense>
        <AppPricesTable
            id="prices"
            :default-add-form-values="defaultAddFormValues"
            :main-fields="mainFields"
            :price-fields="priceFields"
            :items="items"
            :title="title"
            form="formComponentSuppliersPricesTable"
            @add-item="addItem"
            @add-item-price="addItemPrice"
            @deleted="deleted"
            @deleted-prices="deletedPrices"
            @annule-update="annuleUpdated"
            @update-items="updateItems"
            @update-items-prices="updateItemsPrices"/>
    </AppSuspense>
</template>
