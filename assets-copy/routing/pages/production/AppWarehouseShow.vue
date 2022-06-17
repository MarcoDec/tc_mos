<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/warehouseStocksItems'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {FormField} from '../../../types/bootstrap-5'
    import type {TableField} from '../../../types/app-collection-table'
    import {onMounted} from 'vue'

    const icon = 'warehouse'
    const title = 'Entrepots'
    const warehouseformfields: FormField [] = [
        {label: 'Company ', name: 'company', options: [{text: 'MG2C', value: 'MG2C'}, {text: 'TCONCEPT', value: 'TCONCEPT'}, {text: 'TUNISIE CONCEPT', value: 'TUNISIE CONCEPT'}, {text: 'WHETEC', value: 'WHETEC'}], type: 'select'},
        {label: 'Nom', name: 'name', type: 'text'}
    ]
    const fieldsStocks: TableField[] = [
        {
            create: true,
            filter: true,
            label: 'Composant',
            name: 'composantRef',
            sort: true,
            type: 'select',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Produit',
            name: 'produitRef',
            sort: true,
            type: 'select',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Numéro de série',
            name: 'numeroDeSerie',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Localisation',
            name: 'localisation',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantité ',
            name: 'quantite',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Prison',
            name: 'prison',
            sort: true,
            type: 'boolean',
            update: true
        }
    ]
    const Volumefields: TableField[] = [
        {
            create: true,
            filter: true,
            label: 'Ref',
            name: 'ref',
            sort: true,
            type: 'select',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Quantité ',
            name: 'quantite',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Type',
            name: 'type',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fetchItem = useNamespacedActions<Actions>('warehouseStocksItems', ['fetchItem']).fetchItem
    const {Stocksitems} = useNamespacedGetters<Getters>('warehouseStocksItems', ['Stocksitems'])
    const {Volumeitems} = useNamespacedGetters<Getters>('warehouseStocksItems', ['Volumeitems'])
    // console.log('aaaa', Stocksitems)
    onMounted(async () => {
        await fetchItem()
    })
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppBtn icon="trash" variant="danger"/>
    <AppBtn variant="primary">
        transfer
    </AppBtn>
    <div class="row">
        <div class="col-4">
            <AppCardShow :fields="warehouseformfields"/>
        </div>
        <div class="col">
            <AppTabs id="gui-start" class="gui-start-content" vertical>
                <AppTab id="gui-start-main" active title="Stock">
                    <h1>Stocks</h1>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-1">
                                <AppBtn variant="primary">
                                    CSV
                                </AppBtn>
                            </div>
                            <div class="col">
                                <div class="input-group mb-3">
                                    <input id="inputGroupFile02" type="file" class="form-control"/>
                                    <label class="input-group-text" for="inputGroupFile02">Rechercher</label>
                                    <AppBtn variant="success">
                                        Upload
                                    </AppBtn>
                                </div>
                                <p> Format supporté : .csv ( séparé par des points virgules ) </p>
                            </div>
                        </div>
                    </div>
                    <AppCollectionTable :fields="fieldsStocks" :items="Stocksitems"/>
                </AppTab>
                <AppTab id="gui-start-files" title="Volume">
                    <h1>Volume stock</h1>
                    <AppCollectionTable :fields="Volumefields" :items="Volumeitems"/>
                </AppTab>
            </AppTabs>
        </div>
    </div>
</template>
