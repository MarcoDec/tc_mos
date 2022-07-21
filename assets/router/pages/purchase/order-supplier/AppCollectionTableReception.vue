<script setup>
    import AppTable from '../../../../components/table/AppTable'
    import generateItems from '../../../../stores/table/items'
    import {onMounted} from 'vue'
    import {useTableMachine} from '../../../../machine'

    const machineSupp = useTableMachine('machine-supplier-items')
    const suppliersItems = generateItems('supplier-items-reception')

    onMounted(async () => {
        suppliersItems.items = [
            {
                commentaire: 'comm',
                composant: 'composant1',
                create: false,
                date: '2022-03-11',
                id: 1,
                qteConfirme: 11,
                qteRecue: 14,
                sort: false,
                status: 'texte',
                update: true
            },
            {
                commentaire: 'comm',
                composant: 'composant2',
                create: false,
                date: '2022-03-11',
                id: 2,
                qteConfirme: 11,
                qteRecue: 14,
                sort: false,
                status: 'texte',
                update: true

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
            label: 'Quantité Confirmée',
            name: 'qteConfirme',
            sort: true,
            update: false
        },
        {
            label: 'Quantité Reçue',
            name: 'qteRecue',
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
        }
    ]
</script>

<template>
    <AppTable
        id="gestion"
        :fields="fields"
        :machine="machineSupp"
        :store="suppliersItems"/>
</template>
