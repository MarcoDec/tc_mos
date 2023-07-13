<script setup>
    import {computed, ref} from 'vue'
    import AppCustomerListProduct from './AppCustomerListProduct.vue'
    import AppCustomerListOF from './AppCustomerListOF.vue'
    import AppCustomerListCommande from './AppCustomerListCommande.vue'
    import AppCustomerListBL from './AppCustomerListBL.vue'
    import AppCustomerListFacture from './AppCustomerListFacture.vue'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)

    /*const fieldsProduits = [
        {
            create: false,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Réf.',
            name: 'ref ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Désignation ',
            name: 'désignation ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'indice client ',
            name: 'indiceClient ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernière commande',
            name: 'dernièreCommande ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernier OF',
            name: 'dernierOf',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernière facture',
            name: 'dernièreFacture ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix',
            name: 'prix ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité',
            name: 'quantité',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsCommandes = [
        {
            create: false,
            filter: true,
            label: 'Compagnie',
            name: 'compagnie',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Réf',
            name: 'réf',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de livraison confirmée',
            name: 'DateLivraisonConfirmée',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'type de commande',
            name: 'typeCommande',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de validation',
            name: 'dateValidation',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsOF = [
        {
            create: false,
            filter: true,
            label: 'commande client ',
            name: 'commandeClient ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Compagnie gérante',
            name: 'compagnieGérante',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Usine ',
            name: 'usine',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'N°OF',
            name: 'numeroOF',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité',
            name: 'quantité',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité fabriquée',
            name: 'quantiteFabriquée',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité réelle',
            name: 'quantitéRéelle',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de fabrication',
            name: 'dateFabrication',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsBL = [
        {
            create: false,
            filter: true,
            label: 'Bordereau ',
            name: 'bordereau ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Title',
            name: 'title',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date Départ ',
            name: 'dateDépart',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsFactures = [
        {
            create: false,
            filter: true,
            label: 'Id',
            name: 'id',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Invoice Number',
            name: 'invoiceNumber',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Invoice Date',
            name: 'invoiceDate',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Current Place',
            name: 'currentPlace',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Total HT',
            name: 'totalHT',
            sort: true,
            type: 'number',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Total TTC',
            name: 'totalTTC',
            sort: true,
            type: 'text',
            update: true
        }
    ]*/
</script>

<template>
    <div>
        <AppTabs id="gui-start-bottom" class="gui-start-content-bottom">
            <h1>Produits</h1>
            <AppTab id="gui-start-products" active title="Produits" icon="product-hunt" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListProduct/>
                </AppSuspense>
            </AppTab>
            <h1>OF</h1>
            <AppTab id="gui-start-of" active title="OF" icon="file-invoice" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListOF/>
                </AppSuspense>
            </AppTab>
            <h1>Commandes</h1>
            <AppTab id="gui-start-commandes" active title="Commandes" icon="hand-holding-usd" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListCommande/>
                </AppSuspense>
            </AppTab>
            <h1>BL</h1>
            <AppTab id="gui-start-bl" active title="BL" icon="clipboard-list" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListBL/>
                </AppSuspense>
            </AppTab>
            <h1>Factures</h1>
            <AppTab id="gui-start-factures" active title="Factures" icon="file-invoice" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListFacture/>
                </AppSuspense>
            </AppTab>
        </AppTabs>
    </div>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
