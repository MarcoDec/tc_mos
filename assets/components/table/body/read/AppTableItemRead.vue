<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        action: {type: Boolean},
        disableRemove: {type: Boolean},
        enableShow: {type: Boolean},
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        index: {required: true, type: Number},
        item: {required: true, type: Object},
        send: {required: true, type: Function}
    })
    const emits = defineEmits(['show'])
    const normalizedIndex = computed(() => props.index + 1)
    //console.log(props.id ,props.fields)
    async function remove() {
        if (confirm('Voulez-vous vraiment supprimer cet élément ?') === false) {
            return
        }
        props.send('submit')
        await props.item.remove()
        props.send('success')
    }

    function update() {
        props.send('update', {updated: props.item['@id']})
    }
    function show() {
        const regex = /\/api\/.*\/(.*)/
        const results = props.item['@id'].match(regex)
        const id = results[1]
        emits('show', id)
    }
</script>

<template>
    <tr :id="id">
        <td v-if="action" class="text-center">
            <AppBtn v-if="enableShow" class="btnEye" icon="eye" label="Voir" @click="show"/>
            <AppBtn v-if="fields.update" icon="pencil-alt" label="Modifier" @click="update"/>
            <AppBtn v-if="!disableRemove" icon="trash" label="Supprimer" variant="danger" @click="remove"/>
        </td>
        <td class="text-center">
            {{ normalizedIndex }}
        </td>
        <AppTableItemField
            v-for="(field, i) in fields.fields"
            :key="field.name"
            :initial-field="fields.initialFields[i]"
            :field="field"
            :item="item"
            :row="id"/>
    </tr>
</template>

<style scoped>
    .btnEye {
        color: green;
        background-color: transparent;
        border: 0px;
        cursor: pointer;
    }
</style>
