<script setup>
    import {defineProps, computed, ref} from 'vue'
    import AppPricesTableAddItems from './AppPricesTableAddItems.vue'
    import AppPricesTableUpdateItem from './AppPricesTableUpdateItem.vue'
    import AppPricesTableItemsComponentSuppliers from './AppPricesTableItemsComponentSuppliers.vue'
    import AppPricesTableItemsPrices from './AppPricesTableItemsPrices.vue'
    import AppPricesTableUpdateItemPrices from './AppPricesTableUpdateItemPrices.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        fieldsComponenentSuppliers: {required: true, type: Array},
        fieldsComponenentSuppliersPrices: {required: true, type: Array},
        form: {required: true, type: String}
    })
    const emit = defineEmits(['addItemPrice', 'annuleUpdate', 'deleted', 'deletedPrices', 'update', 'updateItems', 'updatePrices', 'updateItemsPrices'])
    const updated = ref(false)
    const priceModified = ref(Array(props.item.prices.length).fill(false))
    function update(){
        emit('update')
        updated.value = true
    }
    function deleted(id){
        emit('deleted', id)
    }
    async function updateItems(item) {
        emit('updateItems', item)
        emit('annuleUpdate')
    }
    function annuleUpdated() {
        emit('annuleUpdate')
        updated.value = false
    }
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
    function deletedPrices(id){
        emit('deletedPrices', id)
    }

    const nbTr = computed(() => {
        const nbItems = props.item.prices.length
        if (nbItems > 0) return nbItems
        return 1
    })
    function range(n){
        return Array.from({length: n}, (value, key) => key + 1)
    }
</script>

<template>
    <template v-if="priceModified.length !== 0">
        <tr v-for="(i, index) in range(nbTr)" :key="index">
            <template v-if="updated === false">
                <td v-if="(index === 0)" :rowspan="range(nbTr).length + 1">
                    <button class="btn btn-icon btn-secondary btn-sm mx-2" :title="item.id" @click="update">
                        <Fa icon="pencil"/>
                    </button>
                    <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted(item.id)">
                        <Fa icon="trash"/>
                    </button>
                </td>
                <template v-for="field in fieldsComponenentSuppliers" :key="field.name">
                    <AppPricesTableItemsComponentSuppliers :field="field" :item="item" :rowspan="range(nbTr).length + 1" :index="index"/>
                    <td v-if="field.name === 'prices' && priceModified[index] === false">
                        <button class="btn btn-icon btn-secondary btn-sm mx-2" :title="item.prices[index].id" @click="updatePrices(index)">
                            <Fa icon="pencil"/>
                        </button>
                        <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deletedPrices(item.prices[index].id)">
                            <Fa icon="trash"/>
                        </button>
                    </td>
                    <td v-else-if="field.name === 'prices' && priceModified[index] === true">
                        <button class="btn btn-icon btn-primary btn-sm mx-2">
                            <Fa icon="check" @click="updateItemsPrices(index, item.prices[index])"/>
                        </button>
                        <button class="btn btn-danger btn-icon btn-sm" @click="annuleUpdatedprices(index)">
                            <Fa icon="times"/>
                        </button>
                    </td>
                </template>
            </template>
            <template v-else>
                <AppPricesTableUpdateItem v-if="(index === 0)" :fields="fieldsComponenentSuppliers" :form="form" :item="item" :rowspan="range(nbTr).length + 1" :index="index" @annule-update="annuleUpdated" @update-items="updateItems"/>
                <td v-if="priceModified[index] === false">
                    <button class="btn btn-icon btn-secondary btn-sm mx-2" :title="item.prices[index].id" @click="updatePrices(index)">
                        <Fa icon="pencil"/>
                    </button>
                    <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deletedPrices(item.prices[index].id)">
                        <Fa icon="trash"/>
                    </button>
                </td>
                <td v-else>
                    <button class="btn btn-icon btn-primary btn-sm mx-2">
                        <Fa icon="check" @click="updateItemsPrices(index, item.prices[index])"/>
                    </button>
                    <button class="btn btn-danger btn-icon btn-sm" @click="annuleUpdatedprices(index)">
                        <Fa icon="times"/>
                    </button>
                </td>
            </template>
            <template v-for="field in fieldsComponenentSuppliersPrices" :key="field.name">
                <template v-if="priceModified[index] === false">
                    <td>
                        <AppPricesTableItemsPrices :field="field" :item="item" :index="index"/>
                    </td>
                </template>
                <template v-else>
                    <AppPricesTableUpdateItemPrices :field="field" :form="form" :item="item" :index="index"/>
                </template>
            </template>
        </tr>
        <AppPricesTableAddItems :fields="fieldsComponenentSuppliersPrices" :form="form" @add-item="addItemPrice"/>
    </template>
    <template v-if="priceModified.length === 0 ">
        <tr v-for="(i, index) in range(nbTr)" :key="index">
            <template v-if="updated === false">
                <td v-if="(index === 0)" :rowspan="range(nbTr).length + 1">
                    <button class="btn btn-icon btn-secondary btn-sm mx-2" :title="item.id" @click="update">
                        <Fa icon="pencil"/>
                    </button>
                    <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted(item.id)">
                        <Fa icon="trash"/>
                    </button>
                </td>
                <template v-for="field in fieldsComponenentSuppliers" :key="field.name">
                    <AppPricesTableItemsComponentSuppliers :field="field" :item="item" :rowspan="range(nbTr).length + 1" :index="index"/>
                </template>
            </template>
            <template v-else>
                <AppPricesTableUpdateItem v-if="(index === 0)" :fields="fieldsComponenentSuppliers" :form="form" :item="item" :rowspan="range(nbTr).length + 1" :index="index" @annule-update="annuleUpdated" @update-items="updateItems"/>
            </template>
        </tr>
        <AppPricesTableAddItems :fields="fieldsComponenentSuppliersPrices" :form="form" @add-item="addItemPrice"/>
    </template>
</template>
