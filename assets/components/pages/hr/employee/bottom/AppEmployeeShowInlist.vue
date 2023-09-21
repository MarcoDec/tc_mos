<script setup>
    import AppEmployeeListFormation from './AppEmployeeListFormation.vue'
    import AppEmployeeListEvenement from './AppEmployeeListEvenement.vue'
    import AppEmployeeListOF from './AppEmployeeListOF.vue'
    import AppEmployeeListClocking from './AppEmployeeListClocking.vue'
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom">
        <AppTab id="gui-start-formations" active title="Formations" icon="book" tabs="gui-start-bottom">
            <AppSuspense>
                <AppEmployeeListFormation/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-evenements" title="Événements" icon="calendar" tabs="gui-start-bottom">
            <AppSuspense>
                <AppEmployeeListEvenement/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-of" title="OF" icon="file-invoice" tabs="gui-start-bottom">
            <AppSuspense>
                <AppEmployeeListOF/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-pointage" title="Pointage" icon="bullseye" tabs="gui-start-bottom">
            <AppSuspense>
                <AppEmployeeListClocking/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
