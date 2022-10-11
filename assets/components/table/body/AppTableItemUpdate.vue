<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        index: {required: true, type: Number},
        item: {required: true, type: Object},
        send: {required: true, type: Function}
    })
    const form = computed(() => `${props.id}-form`)
    const normalizedIndex = computed(() => props.index + 1)

    props.item.initUpdate(props.fields)

    function cancel() {
        props.send('search')
    }

    async function update() {
        props.send('submit')
        try {
            await props.item.update()
            props.send('success')
            props.send('search')
        } catch (e) {
            if (e.status === 422)
                props.send('fail', {violations: e.content})
        }
    }
</script>

<template>
    <tr :id="id" class="table-success">
        <td class="text-center">
            <AppForm :id="form" class="d-inline m-0 p-0" @submit="update">
                <AppBtn icon="check" label="Modifier" type="submit" variant="success"/>
            </AppForm>
            <AppBtn icon="times" label="Annuler" variant="danger" @click="cancel"/>
        </td>
        <td class="text-center">
            {{ normalizedIndex }}
        </td>
        <AppTableFormField
            v-for="field in fields"
            :key="field.name"
            v-model="item.updated"
            :field="field"
            :form="form"/>
    </tr>
</template>
