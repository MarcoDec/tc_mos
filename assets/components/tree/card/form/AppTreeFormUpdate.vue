<script setup>
    import {toRefs} from 'vue'
    const {fields, machine, tree} = toRefs(defineProps({
        fields: {required: true, type: Object},
        machine: {required: true, type: Object},
        tree: {required: true, type: Object}
    }))
    tree.value.selected.initUpdate(fields.value)

    function blur() {
        tree.value.blur()
    }

    async function remove(data) {
        machine.value.send('submit')
        try {
            await tree.value.selected.remove(data)
            machine.value.send('success')
        } catch (violations) {
            machine.value.send('fail', {violations})
        }
    }

    async function submit(data) {
        machine.value.send('submit')
        try {
            await tree.value.selected.update(data)
            tree.value.selected.initUpdate(fields.value)
            machine.value.send('success')
        } catch (violations) {
            machine.value.send('fail', {violations})
        }
    }
</script>

<template>
    <AppTreeForm
        :fields="fields"
        :machine="machine"
        :model-value="tree.selected.updated"
        submit-label="Modifier"
        @submit="submit">
        <template #default="{disabled, label, type}">
            <AppBtn :disabled="disabled" :label="label" :type="type">
                <Fa icon="pencil-alt"/>
                {{ label }}
            </AppBtn>
            <AppBtn
                :disabled="disabled"
                class="ms-1"
                label="Annuler"
                variant="warning"
                @click="blur">
                <Fa icon="times"/>
                Annuler
            </AppBtn>
            <AppBtn
                :disabled="disabled"
                class="ms-1"
                label="Supprimer"
                variant="danger"
                @click="remove">
                <Fa icon="trash"/>
                Supprimer
            </AppBtn>
        </template>
    </AppTreeForm>
</template>
