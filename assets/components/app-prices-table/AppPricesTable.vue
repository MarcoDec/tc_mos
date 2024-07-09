<script setup>
    import {defineProps} from 'vue'
    import AppPricesTableHeaders from './head/AppPricesTableHeaders.vue'
    import AppPricesTableBody from './body/AppPricesTableBody.vue'

    /*const props = */defineProps({
        defaultAddFormValues: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String},
        id: {required: true, type: String},
        items: {required: true, type: Object},
        title: {default: '', required: false, type: String},
        rights: {
            required: true,
            type: Object,
            default: () => ({
                update: false,
                delete: false,
                add: false
            })
        }
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

    function deleted(id) {
        emit('deleted', id)
    }

    function deletedPrices(id) {
        // console.log('deletedPrices', id)
        emit('deletedPrices', id)
    }

    function addItem(formData) {
        emit('addItem', formData)
    }

    function addItemPrice(formData) {
        // console.log('addItemPrice', formData)
        emit('addItemPrice', formData)
    }
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppPricesTableHeaders :main-fields="mainFields" :title="title"/>
        <AppPricesTableBody
            :default-add-form-values="defaultAddFormValues"
            :main-fields="mainFields"
            :price-fields="priceFields"
            :form="form"
            :items="items"
            :rights="rights"
            @deleted="deleted"
            @deleted-prices="deletedPrices"
            @annule-update="annuleUpdated"
            @update-items="updateItems"
            @updated-prices="updateItemsPrices"
            @add-item="addItem"
            @add-item-price="addItemPrice"/>
    </table>
    <slot name="btn"/>
</template>
