<script lang="ts" setup>
    import type {FormValues} from '../../../types/bootstrap-5'
    import type {TableField} from '../../../types/app-collection-table'

    const fields: TableField[] = [
        {
            create: true,
            filter: true,
            label: 'Code',
            name: 'code',
            sort: true,
            sortDir: 'none',
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Nom',
            name: 'name',
            options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
            sort: true,
            sortDir: 'none',
            type: 'select',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Type',
            name: 'type',
            sort: true,
            sortDir: 'none',
            type: 'number',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Auto',
            name: 'auto',
            sort: true,
            sortDir: 'none',
            type: 'boolean',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Limite',
            name: 'limite',
            sort: false,
            sortDir: 'none',
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'cadence',
            name: 'cadence',
            sort: true,
            sortDir: 'none',
            type: 'number',
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Prix',
            name: 'prix',
            sort: true,
            sortDir: 'none',
            type: 'number',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Temps(en ms)',
            name: 'Temps',
            sort: false,
            sortDir: 'none',
            type: 'date',
            update: false
        }
    ]

    const items: FormValues[] = [
        {
            ajout: false,
            cadence: 100,
            code: 'AS 01',
            deletable: true,
            id: 1,
            limite: 'Mise En Bornier',
            name: 'Mise en bornnier',
            prix: null,
            temps: null,
            type: null
        },
        {
            ajout: true,
            cadence: 100,
            code: 'AS 02',
            deletable: false,
            id: 2,
            limite: 'Mise En Bornier',
            name: 'Mise en bornnier',
            prix: null,
            temps: null,
            type: null
        }
    ]
</script>

<template>
    <h1>
        <Fa icon="atom"/>
        <span> Opération</span>
    </h1>
    <AppCollectionTable id="operations" :fields="fields" :items="items"/>
</template>
