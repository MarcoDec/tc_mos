<script setup>
    import {defineProps, ref} from 'vue'
    import AppPricesTableUpdateItemPrices from '../UpdateForms/AppPricesTableUpdateItemPrices.vue'
    import AppPricesTableItemsPrices from './AppPricesTableItemsPrices.vue'
    const props = defineProps({
        item: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String},
        priceModified: {required: true, type: Array},
        index: {required: true, type: Number}
    })
    console.log('props.priceFields', props.priceFields)
    const priceModified = ref(props.priceModified)
    function addItemPrice(formData) {
        const component = props.item['@id']
        emit('addItemPrice', {formData, component})
    }
    function updatePrices(index) {
        emit('updatePrices')
        priceModified.value[index] = true
    }
    async function updateItemsPrices(index, item) {
        emit('updateItemsPrices', item)
        priceModified.value[index] = false
    }
    function annuleUpdatedprices(index) {
        emit('annuleUpdate')
        priceModified.value[index] = false
    }
    function deletedPrices(iri){
        emit('deletedPrices', iri)
    }
</script>

<template>
    <template v-for="field in priceFields" :key="field.name">
        <td v-if="priceModified[index] === false">
            VIF2
<!--            <button class="btn btn-icon btn-secondary btn-sm mx-2" :title="item.prices[index].id"-->
<!--                    @click="updatePrices(index)">-->
<!--                <Fa icon="pencil"/>-->
<!--            </button>-->
<!--            <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deletedPrices(item.prices[index].id)">-->
<!--                <Fa icon="trash"/>-->
<!--            </button>-->
<!--            <td>-->
<!--                <AppPricesTableItemsPrices :field="field" :item="item" :index="index"/>-->
<!--            </td>-->
        </td>
        <td v-else>
            VELSE2
<!--            <button class="btn btn-icon btn-primary btn-sm mx-2">-->
<!--                <Fa icon="check" @click="updateItemsPrices(index, item.prices[index])"/>-->
<!--            </button>-->
<!--            <button class="btn btn-danger btn-icon btn-sm" @click="annuleUpdatedprices(index)">-->
<!--                <Fa icon="times"/>-->
<!--            </button>-->
<!--            <AppPricesTableUpdateItemPrices :field="field" :form="form" :item="item" :index="index"/>-->
        </td>
    </template>
</template>

<style scoped>

</style>