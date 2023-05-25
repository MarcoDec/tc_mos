<script setup>
    import AppShowGuiGen from '../AppShowGuiGen.vue'
    import AppSupplierFormShow from '../../../router/pages/supplier/AppSupplierFormShow.vue'
    import AppTab from '../../tab/AppTab.vue'
    import AppTabs from '../../tab/AppTabs.vue'
    import {useRoute} from 'vue-router'

    const route = useRoute()
</script>

<template>
    <AppShowGuiGen>
        <template #gui-left>
            <AppSuspense> <AppSupplierFormShow/></AppSuspense>
        </template>
        <template #gui-bottom>
            <AppTabs id="gui-bottom">
                <AppTab id="gui-bottom-components" active icon="puzzle-piece" tabs="gui-bottom" title="Fournitures"/>
                <AppTab id="gui-bottom-receipts" icon="receipt" tabs="gui-bottom" title="Réceptions"/>
                <AppTab id="gui-bottom-orders" icon="shopping-cart" tabs="gui-bottom" title="Commandes"/>
            </AppTabs>
        </template>
        <template #gui-right>
            {{ route.params.id_supplier }}
        </template>
    </AppShowGuiGen>
</template>

