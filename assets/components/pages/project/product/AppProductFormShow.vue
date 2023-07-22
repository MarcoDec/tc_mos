<script setup>
    import AppShowProductTabAdmin from './tabs/AppShowProductTabAdmin.vue'
    import AppShowProductTabFichiers from './tabs/AppShowProductTabFichiers.vue'
    import AppShowProductTabGeneral from './tabs/AppShowProductTabGeneral.vue'
    import AppShowProductTabLogistic from './tabs/AppShowProductTabLogistic.vue'
    import AppShowProductTabPrice from './tabs/AppShowProductTabPrice.vue'
    import AppShowProductTabProduction from './tabs/AppShowProductTabProduction.vue'
    import AppShowProductTabProject from './tabs/AppShowProductTabProject.vue'
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <Suspense><AppShowProductTabGeneral/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-project"
            title="Project"
            icon="pencil"
            tabs="gui-start">
            <Suspense><AppShowProductTabProject/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-production"
            title="Production"
            icon="pencil"
            tabs="gui-start">
            <Suspense><AppShowProductTabProduction/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-purchase-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <Suspense><AppShowProductTabLogistic/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-purchase-admin"
            title="Admin"
            icon="pallet"
            tabs="gui-start">
            <Suspense><AppShowProductTabAdmin/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <Suspense><AppShowProductTabFichiers/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-Prix"
            title="Prix"
            icon="circle-dollar-to-slot"
            tabs="gui-start">
            <Suspense><AppShowProductTabPrice/></Suspense>
        </AppTab>
    </AppTabs>
</template>
