<script setup>
    import AppShowGuiGen from '../../AppShowGuiGen.vue'
    import AppSupplierFormShow from './AppSupplierFormShow.vue'
    // import {useRoute} from 'vue-router'
    //
    // const route = useRoute()
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-header>
                <div class="bg-white border-1 border-dark">
                    Supplier
                </div>
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
    </AppSuspense>
</template>

<style>
    .border-dark {
        border-bottom: 1px solid grey;
    }
</style>
