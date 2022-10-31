<script setup>
    const props = defineProps({
        fields: {required: true, type: Object},
        machine: {required: true, type: Object},
        tree: {required: true, type: Object}
    })
    props.tree.selected.initUpdate(props.fields)

    function blur() {
        props.tree.blur()
    }

    async function remove(data) {
        props.machine.send('submit')
        try {
            await props.tree.selected.remove(data)
            props.machine.send('success')
        } catch (violations) {
            props.machine.send('fail', {violations})
        }
    }

    async function submit(data) {
        props.machine.send('submit')
        try {
            await props.tree.selected.update(data)
            props.tree.selected.initUpdate(props.fields)
            props.machine.send('success')
        } catch (violations) {
            props.machine.send('fail', {violations})
        }
    }
</script>

<template>
    <AppTreeForm
        :fields="fields"
        :machine="machine"
        :model-value="props.tree.selected.updated"
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
