<script setup>
    import AppTable from '../../table/AppTablePage.vue'
    import generateItems from '../../../../stores/table/items'
    import {onMounted} from 'vue-demi'
    import {useTableMachine} from '../../../../machine'

    const machineSupp = useTableMachine('machine-supplier-items')
    const suppliersItems = generateItems('supplier-items')

    onMounted(async () => {
        suppliersItems.items = [
            {
                compagnie: 'compagnie1',
                composant: 'composant1',
                create: false,
                date: '2022-03-11',
                etat: 'etat',
                id: 1,
                produit: 'produit1',
                quantite: 11,
                quantiteS: 14,
                ref: 'ref',
                sort: true,
                texte: 'texte',
                update: true
            },
            {
                compagnie: 'compagnie2',
                composant: 'composant2',
                create: false,
                date: '2022-03-19',
                etat: 'etat',
                id: 2,
                produit: 'produit2',
                quantite: 20,
                quantiteS: 14,
                ref: 'ref',
                sort: true,
                texte: 'texte',
                update: true
            }
        ]
    })

    const optionsComposant = [
        {text: 'composant1', value: 'composant1'},
        {text: 'composant2', value: 'composant2'}
    ]
    const optionsCompagnie = [
        {text: 'compagnie1', value: 'compagnie1'},
        {text: 'compagnie2', value: 'compagnie2'}
    ]
    const optionsProduits = [
        {text: 'produit1', value: 'produit1'},
        {text: 'produit2', value: 'produit2'}
    ]
    const fields = [
        {
            label: 'N',
            name: '',
            sort: true,
            update: false
        },
        {
            label: 'N+1',
            name: '',
            sort: true,
            update: false
        },
        {
            label: 'BS',
            name: '',
            sort: true,
            update: false
        },

        {
            create: false,
            filter: true,
            label: 'Composant',
            name: 'composant',
            options: {
                label: value =>
                    optionsComposant.find(option => option.value === value)?.text ?? null,
                options: optionsComposant
            },
            sort: true,
            type: 'select',
            update: false
        },
        {
            create: false,
            filter: true,
            label: 'Produit',
            name: 'produit',
            options: {
                label: value =>
                    optionsProduits.find(option => option.value === value)?.text ?? null,
                options: optionsProduits
            },
            sort: false,
            type: 'select',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Référence Fournisseur',
            name: 'ref',
            sort: true,
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Quantité Souhaitée',
            name: 'quantiteS',
            sort: true,
            update: false
        },
        {
            label: 'Split',
            name: 'split',
            sort: true,
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Date Souhaitée',
            name: 'date',
            sort: false,
            type: 'date',
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Quantité Confimrée',
            name: 'quantite',
            sort: true,
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Date de confirmation',
            name: 'date',
            sort: false,
            type: 'date',
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            update: false
        },
        {
            create: true,
            filter: true,
            label: 'Texte',
            name: 'texte',
            sort: true,
            update: false
        },
        {
            create: false,
            filter: true,
            label: 'Compagnie destinataire',
            name: 'compagnie',
            options: {
                label: value =>
                    optionsCompagnie.find(option => option.value === value)?.text ?? null,
                options: optionsCompagnie
            },
            sort: false,
            type: 'select',
            update: true
        }
    ]
</script>

<template>
    <AppTable
        id="gestion"
        :fields="fields"
        :machine="machineSupp"
        :store="suppliersItems">
        <template #cell(split)="{item}">
            <td><AppBtnSplit :item="item"/></td>
        </template>
    </AppTable>
</template>
