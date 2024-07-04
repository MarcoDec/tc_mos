<script setup>
    import {defineProps} from 'vue'
    import AppPricesTableAddItems from './AppPricesTableAddItems.vue'
    import AppPricesTableItems from './AppPricesTableItems.vue'

    const props = defineProps({
        defaultAddFormValues: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String},
        items: {required: true, type: Object}
    })
    console.log('defaultAddFormValues', props.defaultAddFormValues)
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

    function deleted(id) {
        emit('deleted', id)
    }

    function deletedPrices(id) {
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
    <AppPricesTableAddItems :default-add-form-values="defaultAddFormValues" :fields="mainFields" :form="form" @add-item="addItem"/>
    <AppPricesTableItems
        :main-fields="mainFields"
        :price-fields="priceFields"
        :form="form"
        :items="items"
        @deleted="deleted"
        @deleted-prices="deletedPrices"
        @add-item-price="addItemPrice"
        @annule-update="annuleUpdated"
        @update-items="updateItems"
        @update-items-prices="updateItemsPrices"/>
    </tbody>
</template>
