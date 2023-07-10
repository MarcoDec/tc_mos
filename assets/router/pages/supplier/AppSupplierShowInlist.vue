<script setup>
    import {computed, ref} from 'vue'
    import AppSupplierListFourniture from './AppSupplierListFourniture.vue'
    import AppSupplierListRetard from './AppSupplierListRetard.vue'
    import AppSupplierListCommande from './AppSupplierListCommande.vue'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom">
        <h1>Fournitures</h1>
        <AppTab id="gui-start-fournitures" active title="Fournitures" icon="puzzle-piece" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSupplierListFourniture/>
            </AppSuspense>
        </AppTab>
        <h1>Retards</h1>
        <AppTab id="gui-start-retard" active title="Retard" icon="receipt" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSupplierListRetard/>
            </AppSuspense>
        </AppTab>
        <h1>Commandes</h1>
        <AppTab id="gui-start-commandes" active title="Commandes" icon="shopping-cart" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSupplierListCommande/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
