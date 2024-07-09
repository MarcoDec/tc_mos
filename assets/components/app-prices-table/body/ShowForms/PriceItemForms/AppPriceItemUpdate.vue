<script setup>
    import AppPricesTableUpdateItemPrices from "../../UpdateForms/AppPricesTableUpdateItemPrices.vue"
    import {defineProps, ref} from 'vue'
    const props = defineProps({
        item: {required: true, type: Object},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String}
    })
    const localItem = ref(props.item)
    const emit = defineEmits(['annuleUpdate', 'updateItemsPrices'])
    async function updateItemsPrices(item) {
        emit('updateItemsPrices', item)
    }
    function cancelUpdatedPrices(item) {
        emit('annuleUpdate', item)
    }
</script>

<template>
    <td>
        <button class="btn btn-icon btn-primary btn-sm mx-2">
            <Fa icon="check" @click="updateItemsPrices"/>
        </button>
        <button class="btn btn-danger btn-icon btn-sm" @click="cancelUpdatedPrices">
            <Fa icon="times"/>
        </button>
    </td>
    <AppPricesTableUpdateItemPrices
        v-for="(field, index0) in priceFields"
        :key="`update_form_${index0}_item_${item.id}_field_${field.name}`"
        :field="field"
        :form="form"
        :item="localItem"/>
</template>
