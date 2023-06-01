<script setup>
    import AppBtnJS from '../../AppBtnJS'
    import AppTableItemField from './read/AppTableItemField.vue'
    import {defineProps} from 'vue'
    import {generateTableFields} from '../../props'
    import {useRouter} from 'vue-router'
    import {useWarehouseShowStore} from '../../../stores/logistic/warehouses/warehouseShow.js'

    const emit = defineEmits(['itemShow'])
    const store = useWarehouseShowStore()
    const router = useRouter()

    const props = defineProps({
        fields: generateTableFields(),
        id: {required: true, type: String},
        index: {required: true, type: Number},
        item: {required: true, type: Object},
        machine: {required: true, type: Object},
        options: {default: () => ({delete: true, modify: true, show: true}), required: false, type: Object}
    })
    async function deleteItem() {
        props.machine.send('submit')
        await props.item.remove()
        props.machine.send('success')
        const deletionEvent = new Event('deletion')
        document.dispatchEvent(deletionEvent)
    }
    function showItem() {
        console.debug(`AppTableItemJSinVue showItem ${props.id}`, props.item.id)
        store.setCurrentId(props.item.id)
        emit('itemShow', props.item)
        router.push(`warehouse-show/${props.item.id}`) //{props.item.id}\`
    }
</script>

<template>
    <tr :id="id">
        <td class="text-center">
            {{ index + 1 }}
        </td>
        <td class="text-center">
            <AppBtnJS
                v-if="options.show"
                icon="eye"
                label="Voir"
                title="Voir"
                variant="primary"
                @click="showItem"/>
            <AppBtnJS
                v-if="options.modify"
                icon="pencil-alt"
                label="Modifier"
                title="Modifier"
                variant="primary"
                @click="machine.send('update', {updated: item['@id']})"/>
            <AppBtnJS
                v-if="options['delete']"
                icon="trash"
                label="Supprimer"
                title="Supprimer"
                variant="danger"
                @click="deleteItem"/>
        </td>
        <AppTableItemField
            v-for="field in fields"
            :id="`${id}-${field.name}`"
            :key="field.name"
            :row="id"
            :item="item"
            :machine="machine"
            :field="field"/>
    </tr>
</template>
