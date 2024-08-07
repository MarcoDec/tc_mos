<script setup>
    import {defineProps, ref} from 'vue'
    import AppPricesTableItemLeft from './AppPricesTableItemLeft.vue'
    import AppPricesTableItemRight from './AppPricesTableItemRight.vue'

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
                main: {
                    update: false,
                    delete: false,
                    add: false
                },
                price: {
                    update: false,
                    delete: false,
                    add: false
                }
            })
        }
    })
    // console.log('props', props)
    const emit = defineEmits(['addItemPrice', 'priceDeleted', 'updatedPrices', 'deleted', 'updateItems'])
    const priceModified = ref([])
    priceModified.value = Array.from({length: props.item.prices.length}, () => false)

    function onPriceDeleted(iri) {
        // console.log('onPriceDeleted', iri)
        emit('priceDeleted', iri)
    }

    function onAddItemPrice(formData) {
        // console.log('onAddItemPrice', formData)
        emit('addItemPrice', formData)
    }

    function onUpdatedPrice(item) {
        // console.log('onUpdatedPrice', item)
        emit('updatedPrices', item)
    }

    function onDeleted(iri) {
        // console.log('onDeleted', iri)
        emit('deleted', iri)
    }

    function onUpdateItems(item) {
        // console.log('onUpdateItems', item)
        emit('updateItems', item)
    }
</script>

<template>
    <tr>
        <AppPricesTableItemLeft
            :index="index"
            :form="`${form}_${index}_main`"
            :price-fields="priceFields"
            :main-fields="mainFields"
            :item="item"
            :rights="rights.main"
            @deleted="onDeleted"
            @update-items="onUpdateItems"/>
    </tr>
    <tr>
        <AppPricesTableItemRight
            :index="index"
            :price-modified="priceModified"
            :form="`${form}_${index}_prices`"
            :price-fields="priceFields"
            :main-fields="mainFields"
            :item="item"
            :rights="rights.price"
            @price-deleted="onPriceDeleted"
            @add-item-price="onAddItemPrice"
            @updated-prices="onUpdatedPrice"/>
    </tr>
</template>
