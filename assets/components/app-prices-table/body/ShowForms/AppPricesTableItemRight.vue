<script setup>
    import {defineProps, ref} from 'vue'
    import AppPricesTablePriceItemShow from './AppPricesTablePriceItemShow.vue'
    import AppPricesTableAddItems from '../AddForms/AppPricesTableAddItems.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String},
        index: {required: true, type: Number},
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
    const emit = defineEmits(['priceDeleted', 'addItemPrice', 'updatedPrices'])
    const localItem = ref(props.item)
    // console.log('props.priceFields', props.priceFields)
    const filteredMainFields = props.mainFields.filter(field => !field.children)
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
        emit('addItemPrice', formData)
    }

    function updatePrice(item) {
        emit('updatedPrices', item)
    }
</script>

<template>
    <td/>
    <td :colspan="filteredMainFields.length">
        <table class="table table-bordered table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    <th width="100">
                        Actions
                    </th>
                    <th
                        v-for="(field, index1) in priceFields"
                        :key="`field_${index1}_item_${item.id}`"
                        class="text-center"
                        :width="field.width">
                        {{ field.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <AppPricesTableAddItems
                    v-if="rights.add"
                    :form="`${form}_addPriceItems_${index}`"
                    :fields="priceFields"
                    :default-add-form-values="defaultAddFormValues"
                    @add-item="onAddItem"/>
                <tr v-for="(price, index0) in localItem.prices" :key="`price_item_${item.id}_${index0}`">
                    <AppPricesTablePriceItemShow
                        :item="price"
                        :price-fields="priceFields"
                        :rights="rights"
                        @deleted-prices="deletePrice"
                        @updated-prices="updatePrice"/>
                </tr>
            </tbody>
        </table>
    </td>
</template>
