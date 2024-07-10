<script setup>
    import {defineProps, ref, defineEmits} from 'vue'
    import AppPriceItemUpdate from './PriceItemForms/AppPriceItemUpdate.vue'
    import AppPriceItemShow from './PriceItemForms/AppPriceItemShow.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        priceFields: {required: true, type: Array},
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
    const emit = defineEmits(['deletedPrices', 'updatedPrices'])
    const key = ref(0)
    const localItem = ref({})
    // localItem.value = Object.assign({}, props.item)
    localItem.value = {...props.item}
    // console.log('AppPricesTablePriceItemShow.vue item', props.item)
    const onGoingUpdate = ref(false)

    function deletedPrice(iri) {
        emit('deletedPrices', iri)
    }

    function onPriceToUpdate() {
        onGoingUpdate.value = true
    }

    function onCancelUpdate() {
        // localItem.value = Object.assign({}, props.item)
        localItem.value = {...props.item}
        key.value++
        onGoingUpdate.value = false
    }

    function onUpdateItemPrice() {
        // console.log('onUpdateItemPrice', localItem.value)
        emit('updatedPrices', localItem.value)
    }
</script>

<template>
    <AppPriceItemShow
        v-if="onGoingUpdate === false"
        :key="`price_show_${localItem.id}_${key}`"
        :form="`price_${localItem.id}`"
        :price-fields="priceFields"
        :item="localItem"
        :rights="rights"
        @deleted-price="deletedPrice"
        @price-to-update="onPriceToUpdate"/>
    <AppPriceItemUpdate
        v-else
        :key="`price_update_${localItem.id}_${key}`"
        :form="`price_${localItem.id}`"
        :price-fields="priceFields"
        :item="localItem"
        @annule-update="onCancelUpdate"
        @update-items-prices="onUpdateItemPrice"/>
</template>
