<script setup>
    import {defineProps, ref, computed, defineEmits} from 'vue'
    import AppPriceItemUpdate from "./PriceItemForms/AppPriceItemUpdate.vue";
    import AppPriceItemShow from "./PriceItemForms/AppPriceItemShow.vue";

    const props = defineProps({
        item: {required: true, type: Object},
        priceFields: {required: true, type: Array}
    })
    const emit = defineEmits(['deletedPrices'])
    const key = ref(0)
    const localItem = ref({})
    localItem.value = Object.assign({}, props.item)
    // console.log('AppPricesTablePriceItemShow.vue item', props.item)
    const onGoingUpdate = ref(false)
    function deletedPrice(iri) {
        emit('deletedPrices', iri)
    }
    function onPriceToUpdate(item) {
        onGoingUpdate.value = true
    }
    function onCancelUpdate() {
        localItem.value = Object.assign({}, props.item)
        key.value++
        onGoingUpdate.value = false
    }
    function onUpdateItemPrice() {
        console.log('onUpdateItemPrice', localItem.value)
        emit('updatedPrices', localItem.value)
    }
</script>

<template>
    <AppPriceItemShow
        v-if="onGoingUpdate === false"
        :form="`price_${localItem.id}`"
        :key="`price_show_${localItem.id}_${key}`"
        :price-fields="priceFields"
        :item="localItem"
        @deleted-price="deletedPrice"
        @price-to-update="onPriceToUpdate"/>
    <AppPriceItemUpdate
        v-else
        :form="`price_${localItem.id}`"
        :key="`price_update_${localItem.id}_${key}`"
        :price-fields="priceFields"
        :item="localItem"
        @annule-update="onCancelUpdate"
        @update-items-prices="onUpdateItemPrice"/>
</template>

<style scoped>

</style>