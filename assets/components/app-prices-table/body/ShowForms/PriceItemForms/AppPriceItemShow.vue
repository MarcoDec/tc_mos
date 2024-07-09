<script setup>
    import AppPricesTableItemsPrices from '../AppPricesTableItemsPrices.vue'
    import {defineProps, defineEmits} from 'vue'

    const emits = defineEmits(['deletedPrice', 'priceToUpdate'])
    /*const props =*/ defineProps({
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

    function priceToUpdate(item) {
        // console.log('priceToUpdate', item['@id'])
        emits('priceToUpdate', item)
    }

    function deletedPrices(iri) {
        emits('deletedPrice', iri['@id'])
    }
</script>

<template>
    <td>
        <button
            v-if="rights.update"
            class="btn btn-icon btn-secondary btn-sm mx-2"
            :title="item.id"
            @click="priceToUpdate(item)">
            <Fa icon="pencil"/>
        </button>
        <button
            v-if="rights.delete"
            class="btn btn-danger btn-icon btn-sm mx-2"
            @click="deletedPrices(item)">
            <Fa icon="trash"/>
        </button>
    </td>
    <td v-for="(field, index0) in priceFields" :key="`price_field_${index0}_item_${item.id}`">
        <AppPricesTableItemsPrices :field="field" :item="item"/>
    </td>
</template>
