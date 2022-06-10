<script setup>
    import AppTablePage from '../../AppTablePage'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = [
        {text: '', value: null},
        {text: 'Contrepartie de test', type: 'CounterPartGroup', value: '/api/counter-part-groups'},
        {text: 'Poste de travail', type: 'WorkstationGroup', value: '/api/workstation-groups'},
        {text: 'Outil', type: 'ToolGroup', value: '/api/tool-groups'}
    ]
    const groupFields = [
        {label: 'Code', name: 'code', sort: true},
        {label: 'Nom', name: 'name', sort: true},
        {
            label: 'Type',
            name: '@type',
            options: {label: value => options.find(option => option.type === value)?.text ?? null, options},
            sort: false,
            type: 'select'
        }
    ]

    async function create(data, machine, store) {
        machine.send('submit')
        try {
            const type = data.get('@type')
            if (type === null)
                throw [{message: 'Cette valeur ne doit pas être vide.', propertyPath: '@type'}]
            await store.create(data, type)
            machine.send('success')
        } catch (violations) {
            machine.send('fail', {violations})
        }
    }

    async function goTo(index, machine, store) {
        machine.send('submit')
        await store.goTo(index, store.search['@type'] ?? null)
        machine.send('success')
    }

    async function search(data, machine, store) {
        machine.send('submit')
        await store.fetch(data.get('@type'))
        machine.send('success')
    }
</script>

<template>
    <AppTablePage :fields="groupFields" :icon="icon" :title="title">
        <template #create="{fields, icon: btnIcon, id, inline, machine, noContent, store, submitLabel, variant}">
            <AppForm
                :id="id"
                :fields="fields"
                :inline="inline"
                :no-content="noContent"
                :submit-label="submitLabel"
                @submit="data => create(data, machine, store)">
                <template #default="{disabled, form, submitLabel: label, type}">
                    <AppBtn
                        :disabled="disabled"
                        :form="form"
                        :icon="btnIcon"
                        :title="label"
                        :type="type"
                        :variant="variant"/>
                </template>
            </AppForm>
        </template>
        <template #pagination="{machine, range, store}">
            <AppPaginationItem
                v-for="index in range"
                :key="index"
                :index="index"
                :store="store"
                @click="() => goTo(index, machine, store)"/>
        </template>
        <template #search="{fields, icon: btnIcon, id, inline, machine, noContent, store, submitLabel, variant}">
            <AppForm
                :id="id"
                :fields="fields"
                :inline="inline"
                :no-content="noContent"
                :submit-label="submitLabel"
                @submit="data => search(data, machine, store)">
                <template #default="{disabled, form, submitLabel: label, type}">
                    <AppBtn
                        :disabled="disabled"
                        :form="form"
                        :icon="btnIcon"
                        :title="label"
                        :type="type"
                        :variant="variant"/>
                </template>
            </AppForm>
        </template>
    </AppTablePage>
</template>
