<script setup>
    import {defineProps, computed, ref} from 'vue'
    import AppPricesTableAddItems from '../AddForms/AppPricesTableAddItems.vue'
    import AppPricesTableUpdateItem from '../UpdateForms/AppPricesTableUpdateItem.vue'
    import AppPricesTableItemLeft from './AppPricesTableItemLeft.vue'
    import AppPricesTableItemRight from './AppPricesTableItemRight.vue'
    import AppPricesTableItemsComponentSuppliers from './AppPricesTableItemsComponentSuppliers.vue'
    import AppPricesTableItemsPrices from './AppPricesTableItemsPrices.vue'
    import AppPricesTableUpdateItemPrices from '../UpdateForms/AppPricesTableUpdateItemPrices.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String},
        index: {required: true, type: Number}
    })
    const emit = defineEmits(['addItemPrice', 'annuleUpdate', 'deleted', 'deletedPrices', 'update', 'updateItems', 'updatePrices', 'updateItemsPrices'])
    const updated = ref(false)
    const priceModified = ref([])
    priceModified.value = Array.from({length: props.item.prices.length}, () => false)

    function update() {
        emit('update')
        updated.value = true
    }

    function deleted(item) {
        console.log('deleted', item['@id'])
        emit('deleted', item['@id'])
    }

    async function updateItems(item) {
        emit('updateItems', item)
        emit('annuleUpdate')
    }

    function annuleUpdated() {
        emit('annuleUpdate')
        updated.value = false
    }

    async function updateItemsPrices(index, item) {
        emit('updateItemsPrices', item)
        priceModified.value[index] = false
    }

    function annuleUpdatedprices(index) {
        emit('annuleUpdate')
        priceModified.value[index] = false
    }

    function deletedPrices(iri) {
        emit('deletedPrices', iri)
    }

    console.log('props.item', props.item)
    const nbTr = computed(() => {
        const nbItems = props.item.prices.length
        if (nbItems > 0) return nbItems
        return 1
    })

    function range(n) {
        return Array.from({length: n}, (value, key) => key + 1)
    }

    console.log('priceModified', priceModified.value)
    console.log('range(nbTr)', range(nbTr.value))
</script>

<template>
        <tr>World</tr>
        <tr>
            <AppPricesTableItemLeft
                :index="index"
                :form="form"
                :price-fields="priceFields"
                :main-fields="mainFields"
                :item="item"
            />
            <AppPricesTableItemRight
                :index="index"
                :price-modified="priceModified"
                :form="form"
                :price-fields="priceFields"
                :main-fields="mainFields"
                :item="item"/>
        </tr>
</template>
