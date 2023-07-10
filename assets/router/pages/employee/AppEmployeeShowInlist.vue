<script setup>
    import {computed, ref} from 'vue'
    import AppEmployeeListFormation from './AppEmployeeListFormation.vue'
    import AppEmployeeListEvenement from './AppEmployeeListEvenement.vue'
    import AppEmployeeListOF from './AppEmployeeListOF.vue'
    import AppEmployeeListClocking from './AppEmployeeListClocking.vue'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)

    // const fieldsFormations = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Sujet ',
    //         name: 'sujet ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de début',
    //         name: 'dateDébut ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de fin ',
    //         name: 'dateFin ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'date de rappel',
    //         name: 'dateRappel  ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Niveau',
    //         name: 'niveau ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'formateur',
    //         name: 'Formateur',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Dernière facture',
    //         name: 'dernièreFacture ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Prix',
    //         name: 'prix ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité',
    //         name: 'quantité',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     }
    // ]
    // const fieldsEvenements = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de l événement',
    //         name: 'dateEvénement',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Description ',
    //         name: 'description ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Motif',
    //         name: 'motif',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     }
    // ]

    // const fieldsOF = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'OF',
    //         name: 'OF',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Produit',
    //         name: 'produit',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité fabriquée ',
    //         name: 'quantitéFabriquée',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Rebus',
    //         name: 'rebus',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Cadence',
    //         name: 'cadence',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     }
    // ]

    // const fieldsFactures = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'date de début ',
    //         name: 'dateDébut',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'date de fin',
    //         name: 'dateFin',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     }
    // ]
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
