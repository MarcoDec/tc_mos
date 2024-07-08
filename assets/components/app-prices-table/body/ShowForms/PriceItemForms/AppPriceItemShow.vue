<script setup>
import AppPricesTableItemsPrices from "../AppPricesTableItemsPrices.vue"
import {defineProps, defineEmits} from 'vue'

const emits = defineEmits(['deletePrice', 'priceToUpdate'])
const props = defineProps({
    item: {required: true, type: Object},
    priceFields: {required: true, type: Array},
    form: {required: true, type: String}
})

function priceToUpdate(item) {
    console.log('priceToUpdate', item['@id'])
    //emits('priceToUpdate', item)
}

function deletedPrices(iri) {
    emits('deletedPrice', iri['@id'])
}
</script>

<template>
    <td>
        <button
            class="btn btn-icon btn-secondary btn-sm mx-2"
            :title="item.id"
            @click="priceToUpdate(item)">
            <Fa icon="pencil"/>
        </button>
        <button
            class="btn btn-danger btn-icon btn-sm mx-2"
            @click="deletedPrices(item)">
            <Fa icon="trash"/>
        </button>
    </td>
    <td v-for="field in priceFields">
        <AppPricesTableItemsPrices :field="field" :item="item"/>
    </td>
</template>

<style scoped>

</style>