<script setup>
    import {defineProps, ref, computed, defineEmits} from 'vue'
    import AppPriceItemUpdate from "./PriceItemForms/AppPriceItemUpdate.vue";
    import AppPriceItemShow from "./PriceItemForms/AppPriceItemShow.vue";

    const props = defineProps({
        item: {required: true, type: Object},
        priceFields: {required: true, type: Array}
    })
    const emit = defineEmits(['deletedPrices'])
    // console.log('AppPricesTablePriceItemShow.vue item', props.item)
    const onGoingUpdate = ref(false)
    function deletedPrice(iri) {
        emit('deletedPrices', iri)
    }
</script>

<template>
    <AppPriceItemShow
        v-if="onGoingUpdate === false"
        :form="`price_${item.id}`"
        :price-fields="priceFields"
        :item="item"
        @deleted-price="deletedPrice"/>
    <AppPriceItemUpdate
        v-else
        :form="`price_${item.id}`"
        :price-fields="priceFields"
        :item="item"/>
</template>

<style scoped>

</style>