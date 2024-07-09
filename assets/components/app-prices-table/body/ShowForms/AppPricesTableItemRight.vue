<script setup>
    import {defineProps, ref} from 'vue'
    import AppPricesTableUpdateItemPrices from '../UpdateForms/AppPricesTableUpdateItemPrices.vue'
    import AppPricesTableItemsPrices from './AppPricesTableItemsPrices.vue'
    import AppPricesTablePriceItemShow from "./AppPricesTablePriceItemShow.vue";
    import api from "../../../../api";
    import AppPricesTableAddItems from "../AddForms/AppPricesTableAddItems.vue";
    const props = defineProps({
        item: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String},
        priceModified: {required: true, type: Array},
        index: {required: true, type: Number}
    })
    const emit = defineEmits(['priceDeleted','addItemPrice', 'updatedPrices'])
    const localItem = ref(props.item)
    // console.log('props.priceFields', props.priceFields)
    const filteredMainFields = props.mainFields.filter(field => !field.children)
    const priceModified = ref(props.priceModified)
    const defaultAddFormValues = {
        item: props.item['@id'],
        price: {
            code: '/api/currencies/1',
            value: 0
        },
        quantity: {
            value: 0,
            code: '/api/units/1'
        },
        ref: null
    }
    async function deletePrice(iri) {
        emit('priceDeleted', iri)
    }
    function onAddItem(formData) {
        console.log('addItemPrice', formData)
        emit('addItemPrice', formData)
    }
    function updatePrice(item) {
        console.log('updatePrice', item)
        emit('updatedPrices', item)
    }
</script>

<template>
    <td></td>
    <td :colspan="filteredMainFields.length">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th width="100">Actions</th>
                    <th
                        v-for="field in priceFields"
                        class="text-center"
                        :key="field.name"
                        :width="field.width">
                        {{ field.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <AppPricesTableAddItems
                    :form="`addPriceItems_${index}`"
                    :fields="priceFields"
                    :default-add-form-values="defaultAddFormValues"
                    @add-item="onAddItem"/>
                <tr v-for="price in localItem.prices">
                    <AppPricesTablePriceItemShow
                        :item="price"
                        :price-fields="priceFields"
                        @deleted-prices="deletePrice"
                        @updated-prices="updatePrice"/>
                </tr>
            </tbody>
        </table>
    </td>
</template>

<style scoped>

</style>