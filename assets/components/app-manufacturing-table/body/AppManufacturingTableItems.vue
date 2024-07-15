<script setup>
    import AppManufacturingTableItem from './AppManufacturingTableItem.vue'
    import {defineProps, ref} from 'vue'
    import api from "../../../api"

    const emits = defineEmits(['oFsConfirmed'])
    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        id: {required: true, type: String},
        items: {required: true, type: Array},
        title: {required: true, type: String}
    })
    const localItems = ref([])
    localItems.value = props.items
    const lengthTable = props.fields.length - 1

    function confirmOFs() {
        const promises = []
        localSelectedItems.value.forEach(item => {
            // /api/manufacturing-orders/2/promote/manufacturing_order/to/accept
            const apiRequest = api(`/api/manufacturing-orders/${item.manufacturingOrderId}/promote/manufacturing_order/to/accept`, 'PATCH')
            promises.push(apiRequest)
        })
        Promise.all(promises).then(() => {
            emits('oFsConfirmed')
        })
    }

    const localSelectedItems = ref([])
    function addSelectedItems(item) {
        localSelectedItems.value.push(item)
    }
    function removeSelectedItems(item) {
        const index = localSelectedItems.value.findIndex(i => i.id === item.id)
        localSelectedItems.value.splice(index, 1)
    }
    function onModelValueUpdated(item, newItem) {
        const index = localItems.value.findIndex(i => i.id === item.id)
        localItems.value[index] = newItem
        //On met à jour les items sélectionnés. Si l'item a sa propriété confirmerOF à true, on l'ajoute sinon on le retire
        if (newItem.confirmedOF) {
            console.log('addSelectedItems')
            addSelectedItems(newItem)
        } else {
            console.log('removeSelectedItems')
            removeSelectedItems(newItem)
        }
        console.log('selectedItems', localSelectedItems.value)
    }
</script>

<template>
    <tbody>
        <AppManufacturingTableItem
            v-for="item in localItems"
            :id="id"
            :key="item.id"
            :item="item"
            :fields="fields"
            :title="title"
            :form="form"
            @update:model-value="(newItem) => onModelValueUpdated(item, newItem)"/>
        <tr v-if="title === 'collapse new Ofs'">
            <td :colspan="lengthTable" class="bg-white text-center text-white"/>
            <td>
                <AppBtn type="submit" variant="success">
                    Générer OFs
                </AppBtn>
            </td>
        </tr>
        <tr v-else-if="title === 'collapse ofs ToConfirm'">
            <td :colspan="lengthTable" class="bg-white text-center text-white"/>
            <td>
                <AppBtn type="submit" variant="success" @click="confirmOFs">
                    Confirmer OFs
                </AppBtn>
            </td>
        </tr>
    </tbody>
</template>
