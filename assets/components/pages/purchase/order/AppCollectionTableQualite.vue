<script setup>
    import AppTable from '../../table/AppTablePage.vue'
    import generateItems from '../../../../stores/table/items'
    import {onMounted} from 'vue'
    import {useTableMachine} from '../../../../machine'

    const machineSupp = useTableMachine('machine-supplier-items')
    const suppliersItems = generateItems('supplier-items-qualite')

    onMounted(async () => {
        suppliersItems.items = [
            {
                commentaire: 'comm',
                composant: 'composant1',
                controle: '',
                create: false,
                date: '2022-03-11',
                id: 1,
                sort: true,
                status: 'texte',
                update: true,
                valeur: '12'
            },
            {
                commentaire: 'comm',
                composant: 'composant1',
                create: false,
                date: '2022-03-11',
                id: 2,
                qteConfirme: 11,
                qteRecue: 14,
                sort: true,
                status: 'texte',
                update: true,
                valeur: '12'
            }
        ]
    })

    const fields = [
        {
            label: 'Composant',
            name: 'composant',
            sort: true,
            update: false
        },
        {
            label: 'Date Récéption',
            name: 'date',
            sort: true,
            update: false
        },
        {
            create: true,
            label: 'Status',
            name: 'status',
            sort: true,
            update: false
        },

        {
            create: true,
            filter: true,
            label: 'Commentaire',
            name: 'commentaire',
            sort: true,
            update: false
        },
        {
            label: 'Controle',
            name: 'controle',
            sort: true,
            update: false
        },
        {
            label: 'Valeur',
            name: 'valeur',
            sort: true,
            update: false
        }
    ]
</script>

<template>
    <AppTable
        id="qualite"
        :fields="fields"
        :machine="machineSupp"
        :store="suppliersItems"/>
</template>
