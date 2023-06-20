<script setup>
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppSupplierFormShow from './AppSupplierFormShow.vue'
    // import {useRoute} from 'vue-router'
    //
    // const route = useRoute()
</script>

<template>
    <AppShowGuiGen>
        <template #gui-header>
            Supplier
        </template>
        <template #gui-left>
            <AppSuspense> <AppSupplierFormShow/></AppSuspense>
        </template>
        <template #gui-bottom>
            <!--            <AppTabs id="gui-bottom">-->
            <!--                <AppTab id="gui-bottom-components" active icon="puzzle-piece" tabs="gui-bottom" title="Fournitures"/>-->
            <!--                <AppTab id="gui-bottom-receipts" icon="receipt" tabs="gui-bottom" title="Réceptions"/>-->
            <!--                <AppTab id="gui-bottom-orders" icon="shopping-cart" tabs="gui-bottom" title="Commandes"/>-->
            <!--            </AppTabs>-->
        </template>
        <template #gui-right/>
    </AppShowGuiGen>
</template>

