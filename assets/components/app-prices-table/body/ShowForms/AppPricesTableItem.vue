<script setup>
    import {defineProps, computed, ref} from 'vue'
    import AppPricesTableItemLeft from './AppPricesTableItemLeft.vue'
    import AppPricesTableItemRight from './AppPricesTableItemRight.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String},
        index: {required: true, type: Number}
    })
    console.log('props', props)
    const emit = defineEmits(['addItemPrice', 'priceDeleted', 'updatedPrices', 'deleted'])
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

    // console.log('props.item', props.item)
    const nbTr = computed(() => {
        const nbItems = props.item.prices.length
        if (nbItems > 0) return nbItems
        return 1
    })

    function range(n) {
        return Array.from({length: n}, (value, key) => key + 1)
    }

    // console.log('priceModified', priceModified.value)
    // console.log('range(nbTr)', range(nbTr.value))
    function onPriceDeleted(iri) {
        console.log('onPriceDeleted', iri)
        emit('priceDeleted', iri)
    }
    function onAddItemPrice(formData) {
        console.log('onAddItemPrice', formData)
        emit('addItemPrice', formData)
    }
    function onUpdatedPrice(item) {
        console.log('onUpdatedPrice', item)
        emit('updatedPrices', item)
    }
    function onDeleted(iri) {
        console.log('onDeleted', iri)
        emit('deleted', iri)
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
            @deleted="onDeleted"/>
    </tr>
    <tr>
        <AppPricesTableItemRight
            :index="index"
            :price-modified="priceModified"
            :form="`${form}_${index}_prices`"
            :price-fields="priceFields"
            :main-fields="mainFields"
            :item="item"
            @price-deleted="onPriceDeleted"
            @add-item-price="onAddItemPrice"
            @updated-prices="onUpdatedPrice"/>

    </tr>
</template>
