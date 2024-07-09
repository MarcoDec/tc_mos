<script setup>
    import {computed, defineProps, ref} from 'vue'
    import AppPricesTableItemsComponentSuppliers from './AppPricesTableItemsComponentSuppliers.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        priceFields: {required: true, type: Array},
        form: {required: true, type: String},
        index: {required: true, type: Number}
    })
    const emit = defineEmits(['deleted', 'update', 'annuleUpdate', 'updateItems'])
    console.log('props.item', props.item)
    const updated = ref(false)

    function update() {
        emit('update')
        updated.value = true
    }

    function annuleUpdated() {
        emit('annuleUpdate')
        updated.value = false
    }

    function deleted(item) {
        console.log('deleted', item['@id'])
        emit('deleted', item['@id'])
    }

    async function updateItems(item) {
        emit('updateItems', item)
        emit('annuleUpdate')
    }

    const nbTr = computed(() => {
        const nbItems = props.item.prices.length
        if (nbItems > 0) return nbItems
        return 1
    })

    function range(n) {
        return Array.from({length: n}, (value, key) => key + 1)
    }
</script>

<template>
    <template v-if="updated === false">
        <td class="">
            <button class="btn btn-icon btn-secondary btn-sm mx-2" :title="item.id" @click="update">
                <Fa icon="pencil"/>
            </button>
            <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted(item)">
                <Fa icon="trash"/>
            </button>
        </td>
        <template v-for="field in mainFields" :key="field.name">
            <AppPricesTableItemsComponentSuppliers
                :field="field"
                :item="item"
                :index="index"/>
        </template>
    </template>
    <template v-else>
        <td>VELSE 1</td>
<!--        <template v-for="(field, index0) in mainFields" :key="field.name">-->
<!--            <AppPricesTableUpdateItem-->
<!--                :fields="mainFields"-->
<!--                :form="form"-->
<!--                :item="item"-->
<!--                :rowspan="range(nbTr).length + 1"-->
<!--                :index="index0"-->
<!--                @annule-update="annuleUpdated"-->
<!--                @update-items="updateItems"/>-->
<!--        </template>-->
    </template>
</template>
