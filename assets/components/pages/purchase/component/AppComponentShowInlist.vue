<script setup>
    import AppComponentListCasEmplois from './bottom/AppComponentListCasEmplois.vue'
    import AppComponentListOF from './bottom/AppComponentListOF.vue'
    import AppComponentListBesoinEnregistre from './bottom/AppComponentListBesoinEnregistre.vue'
    import AppComponentListStock from './bottom/AppComponentListStock.vue'
    import AppComponentListReferenceValue from './bottom/AppComponentListReferenceValue.vue'
    import AppComponentListReference from './bottom/AppComponentListReference.vue'
    import AppComponentListReceipt from './bottom/AppComponentListReceipt.vue'
    import AppComponentListEvenementQualite from './bottom/AppComponentListEvenementQualite.vue'
</script>

<template>
    <div>
        <AppTabs id="gui-start-bottom" class="gui-start-content-bottom" format-nav="block">
            <AppTab id="gui-start-casEmplois" active title="Cas d'emplois" icon="list-squares" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListCasEmplois/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-outils" title="OF en cours" icon="screwdriver-wrench" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListOF/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-besoinEnregistre" title="Besoins enregistrés" icon="shopping-cart" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListBesoinEnregistre/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-stocks" title="Stocks" icon="shopping-cart" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListStock/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-evenement-reference_value" title="Valeur de Référence" icon="gear" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListReferenceValue/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-controle-reference" title="Référence de dimension" icon="ruler-horizontal" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListReference/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-controle-reception" title="Contrôle réception" icon="receipt" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListReceipt/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-evenement-qualite" title="Evènements qualités" icon="calendar-day" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListEvenementQualite/>
                </AppSuspense>
            </AppTab>
        </AppTabs>
    </div>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
