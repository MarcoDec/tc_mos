<script lang="ts" setup>
    import type {FormValues} from '../../types/bootstrap-5'
    import type {TableField} from '../../types/app-collection-table'
    import {defineProps} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[], icon: string, title: string}>()
    const route = useRoute()

    const items: FormValues[] = [
        {
            ajout: false,
            cadence: 100,
            code: 'AS 01',
            deletable: true,
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
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items"/>
</template>
