<script setup>
    import AppPricesTableUpdateItemPrices from "../../UpdateForms/AppPricesTableUpdateItemPrices.vue"
    import {defineProps, ref} from 'vue'
    const props = defineProps({
        item: {required: true, type: Object},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String}
    })
    const emit = defineEmits(['annuleUpdate', 'updateItemsPrices'])
    const priceModified = ref([])
    async function updateItemsPrices(index, item) {
        emit('updateItemsPrices', item)
        priceModified.value[index] = false
    }
    function cancelUpdatedPrices(index) {
        emit('annuleUpdate')
        priceModified.value[index] = false
    }
</script>

<template>
    <td>
        <button class="btn btn-icon btn-primary btn-sm mx-2">
            <Fa icon="check" @click="updateItemsPrices(index, item.prices[index])"/>
        </button>
        <button class="btn btn-danger btn-icon btn-sm" @click="cancelUpdatedPrices(index)">
            <Fa icon="times"/>
        </button>
    </td>
    <AppPricesTableUpdateItemPrices :field="field" :form="form" :item="item" :index="index"/>
</template>

<style scoped>

</style>