<script setup>
    import {computed, ref} from 'vue'
    import AppEmployeeListFormation from './AppEmployeeListFormation.vue'
    import AppEmployeeListEvenement from './AppEmployeeListEvenement.vue'
    import AppEmployeeListOF from './AppEmployeeListOF.vue'
    import AppEmployeeListClocking from './AppEmployeeListClocking.vue'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom">
        <h1>Formations</h1>
        <AppTab id="gui-start-formations" active title="Formations" icon="book" tabs="gui-start-bottom">
            <AppSuspense>
                <AppEmployeeListFormation/>
            </AppSuspense>
        </AppTab>
        <h1>Événements</h1>
        <AppTab id="gui-start-evenements" active title="Événements" icon="calendar" tabs="gui-start-bottom">
            <AppSuspense>
                <AppEmployeeListEvenement/>
            </AppSuspense>
        </AppTab>
        <h1>OF</h1>
        <AppTab id="gui-start-of" active title="OF" icon="file-invoice" tabs="gui-start-bottom">
            <AppSuspense>
                <AppEmployeeListOF/>
            </AppSuspense>
        </AppTab>
        <h1>Pointage</h1>
        <AppTab id="gui-start-pointage" active title="Pointage" icon="bullseye" tabs="gui-start-bottom">
            <AppSuspense>
                <AppEmployeeListClocking/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
    <!-- <AppTab id="gui-start-equipments" icon="tools" title="Équipements">
        <AppTable :id="route.name" :fields="fields  " :store="products" :machine="machine"/>
    </AppTab> -->
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
