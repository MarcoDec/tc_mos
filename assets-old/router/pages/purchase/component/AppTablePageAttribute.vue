<script setup>
    import AppTablePage from '../../AppTablePage'
    import generateOptions from '../../../../stores/options'
    import {onUnmounted} from 'vue'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const types = [
        {text: 'Booléen', value: 'bool'},
        {text: 'Couleur', value: 'color'},
        {text: 'Entier', value: 'int'},
        {text: 'Pourcentage', value: 'percent'},
        {text: 'Texte', value: 'text'},
        {text: 'Unité', value: 'unit'}
    ]
    const units = generateOptions('units')
    await units.fetch()
    const fields = [
        {create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true},
        {create: true, label: 'Description', name: 'description', search: true, sort: true, update: true},
        {
            create: true,
            label: 'Type',
            name: 'type',
            options: {label: value => types.find(option => option.value === value)?.text ?? null, options: types},
            search: true,
            sort: true,
            type: 'select',
            update: false
        },
        {
            create: true,
            label: 'Unité',
            name: 'unit',
            options: units,
            search: true,
            sort: true,
            sortName: 'unit.name',
            type: 'select',
            update: true
        },
        {create: false, label: 'Familles', name: 'familiesName', search: false, sort: false, update: false}
    ]

    async function click(attribute, callback) {
        if (confirm(`Voulez-vous vraiment supprimer « ${attribute.name} » ? Cet attribut est associé à des familles.`))
            await callback()
    }

    onUnmounted(() => units.dispose())
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title">
        <template #remove="{icon: btnIcon, item, onClick, title: btnTitle, variant}">
            <AppBtn :icon="btnIcon" :title="btnTitle" :variant="variant" @click="click(item, onClick)"/>
        </template>
    </AppTablePage>
</template>
