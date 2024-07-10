<script setup>
    import {defineProps, ref} from 'vue'
    import AppPricesTableItemsComponentSuppliers from '../AppPricesTableItemsComponentSuppliers.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        mainFields: {required: true, type: Array},
        index: {required: true, type: Number},
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
    const emit = defineEmits(['deleted', 'update'])
    const localItem = ref({})
    localItem.value = {...props.item}
    // localItem.value = Object.assign({}, props.item)

    function update() {
        // console.log('update', localItem.value)
        emit('update', localItem.value)
    }

    function deleted(item) {
        // console.log('deleted', item['@id'])
        emit('deleted', item['@id'])
    }
</script>

<template>
    <td class="">
        <button
            v-if="rights.update"
            class="btn btn-icon btn-secondary btn-sm mx-2"
            :title="localItem.id"
            @click="update">
            <Fa icon="pencil"/>
        </button>
        <button v-if="rights.delete" class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted(localItem)">
            <Fa icon="trash"/>
        </button>
    </td>
    <template v-for="field in mainFields" :key="field.name">
        <AppPricesTableItemsComponentSuppliers
            :field="field"
            :item="localItem"
            :index="index"/>
    </template>
</template>
