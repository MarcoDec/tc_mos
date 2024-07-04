<script setup>
    import {defineProps} from 'vue'
    import AppPricesTableAddItems from './AddForms/AppPricesTableAddItems.vue'
    import AppPricesTableItem from './ShowForms/AppPricesTableItem.vue'

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
    </tbody>
</template>
